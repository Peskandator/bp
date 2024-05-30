<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Majetek\ORM\MovementRepository;
use App\Odpisy\Action\RegenerateDepreciationsAccountingDataAction;
use App\Odpisy\Components\DbfFileGenerator;
use App\Odpisy\Components\DepreciationsAccountingDataGenerator;
use App\Odpisy\Forms\EditDepreciationsAccountingDataFormFactory;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\FlashMessageType;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Form;
use Shuchkin\SimpleXLSXGen;

final class AccountingPresenter extends BaseAccountingEntityPresenter
{
    private DepreciationsAccountingDataGenerator $accountingDataGenerator;
    private MovementRepository $movementRepository;
    private EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory;
    private RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction;
    private SimpleXLSXGen $XLSXGen;
    private DbfFileGenerator $dbfFileGenerator;

    public function __construct(
        DepreciationsAccountingDataGenerator $accountingDataGenerator,
        MovementRepository $movementRepository,
        EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory,
        RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction,
        SimpleXLSXGen $XLSXGen,
        DbfFileGenerator $dbfFileGenerator,
    )
    {
        parent::__construct();
        $this->accountingDataGenerator = $accountingDataGenerator;
        $this->movementRepository = $movementRepository;
        $this->editAccountingDataFormFactory = $editAccountingDataFormFactory;
        $this->regenerateDepreciationsAccountingDataAction = $regenerateDepreciationsAccountingDataAction;
        $this->XLSXGen = $XLSXGen;
        $this->dbfFileGenerator = $dbfFileGenerator;
    }

    public function actionDepreciations(?int $year = null): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Zaúčtování',
                null)
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Odpisů',
                null)
        );

        $selectedYear = $year;
        if (!$selectedYear) {
            $today = new \DateTimeImmutable('today');
            $selectedYear = (int)$today->format('Y');
        }

        $this->template->selectedYear = $selectedYear;
        $this->template->availableYears = $this->currentEntity->getAvailableYearsAccounting();

        $accountingDataForYear = $this->currentEntity->getDepreciationsAccountingDataForYear($selectedYear);
        if ($accountingDataForYear) {
            $accountingData = $this->accountingDataGenerator->updateDepreciationsAccountingData($accountingDataForYear);
        } else {
            $accountingData = $this->accountingDataGenerator->createDepreciationsAccountingData($this->currentEntity, $selectedYear);
        }

        $this->template->accountingData = $accountingData;
        $data = $accountingData->getArrayData();
        $this->template->assetArray = $this->getAssetData($data);
    }

    public function actionExportXlsx(?int $year = null): void
    {
        $selectedYear = $year;
        if (!$selectedYear) {
            $today = new \DateTimeImmutable('today');
            $selectedYear = (int)$today->format('Y');
        }

        $accountingDataForYear = $this->currentEntity->getDepreciationsAccountingDataForYear($selectedYear);
        if (!$accountingDataForYear) {
            return;
        }

        $data = $accountingDataForYear->getArrayData();
        $dataToExport = $this->getDataForExcelExport($data);

        $xlsx = $this->XLSXGen::fromArray($dataToExport, 'Zaúčtování odpisů ' . $year);
        $xlsx->downloadAs('Zaúčtování odpisů ' . $year);
    }

    public function actionExportDbf(?int $year = null): void
    {
        $selectedYear = $year;
        if (!$selectedYear) {
            $today = new \DateTimeImmutable('today');
            $selectedYear = (int)$today->format('Y');
        }

        $accountingDataForYear = $this->currentEntity->getDepreciationsAccountingDataForYear($selectedYear);
        if (!$accountingDataForYear) {
            return;
        }
        $dbfFilePath = $this->dbfFileGenerator->create($accountingDataForYear);
        $this->dbfFileGenerator->addRecordsToTable($accountingDataForYear, $dbfFilePath);

        $response = new FileResponse($dbfFilePath, "nevim.dbf", 'application/dbf');
        $this->sendResponse($response);
    }

    protected function createComponentEditDepreciationsAccountingDataForm(): Form
    {
        $accountingData = $this->template->accountingData;
        $form = $this->editAccountingDataFormFactory->create($accountingData);
        return $form;
    }

    protected function createComponentRegenerateDepreciationsAccountingDataForm(): Form
    {
        $accountingData = $this->template->accountingData;
        $form = new Form;
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($accountingData) {
            $this->regenerateDepreciationsAccountingDataAction->__invoke($accountingData);
            $form->getPresenter()->flashMessage(
                'Data byla znovu vygenerována.',
                FlashMessageType::SUCCESS)
            ;
            $form->getPresenter()->redirect('this');
        };

        return $form;
    }

    protected function getAssetData(array $data): array
    {
        $assets = [];
        foreach ($data as $row) {
            $movementId = $row['movementId'];
            $code = $row['code'];
            $movement = $this->movementRepository->find($movementId);
            $asset = $movement->getAsset();
            $assets[$code] = $asset;
        }

        return $assets;
    }

    protected function getDataForExcelExport(array $data): array
    {
        $records = [];

        $firstRow = ['<b>Majetek</b>', '<b>Datum</b>', '<b>Účet</b>', '<b>MD</b>', '<b>DAL</b>', '<b>ZC</b>', '<b>Popis</b>'];

        $records[] = $firstRow;

        foreach ($data as $row) {
            $record = [];

            $movement = $this->movementRepository->find($row['movementId']);
            $asset = $movement->getAsset();

            $record[] = $asset->getName();
            $date = new \DateTime($row['executionDate']);
            $record[] = $date->format('j. n. Y');
            $record[] = $row['account'];
            $record[] = $row['debitedValue'];
            $record[] = $row['creditedValue'];
            $record[] = $row['residualPrice'];
            $record[] = $row['description'];

            $records[] = $record;
        }

        return $records;
    }
}