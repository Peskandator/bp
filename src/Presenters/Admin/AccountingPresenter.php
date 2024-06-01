<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Majetek\ORM\AssetRepository;
use App\Odpisy\Action\RegenerateDepreciationsAccountingDataAction;
use App\Odpisy\Components\DbfFileGenerator;
use App\Odpisy\Components\DepreciationsAccountingDataGenerator;
use App\Odpisy\Components\XLSXFileGenerator;
use App\Odpisy\Forms\EditDepreciationsAccountingDataFormFactory;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\FlashMessageType;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Form;
use Shuchkin\SimpleXLSXGen;

final class AccountingPresenter extends BaseAccountingEntityPresenter
{
    private DepreciationsAccountingDataGenerator $accountingDataGenerator;
    private EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory;
    private RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction;
    private SimpleXLSXGen $XLSXGen;
    private DbfFileGenerator $dbfFileGenerator;
    private XLSXFileGenerator $XLSXFileGenerator;
    private AssetRepository $assetRepository;

    public function __construct(
        DepreciationsAccountingDataGenerator $accountingDataGenerator,
        EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory,
        RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction,
        SimpleXLSXGen $XLSXGen,
        DbfFileGenerator $dbfFileGenerator,
        XLSXFileGenerator $XLSXFileGenerator,
        AssetRepository $assetRepository,
    )
    {
        parent::__construct();
        $this->accountingDataGenerator = $accountingDataGenerator;
        $this->editAccountingDataFormFactory = $editAccountingDataFormFactory;
        $this->regenerateDepreciationsAccountingDataAction = $regenerateDepreciationsAccountingDataAction;
        $this->XLSXGen = $XLSXGen;
        $this->dbfFileGenerator = $dbfFileGenerator;
        $this->XLSXFileGenerator = $XLSXFileGenerator;
        $this->assetRepository = $assetRepository;
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
        $dataToExport = $this->XLSXFileGenerator->generateContent($accountingDataForYear);
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
            $assetId = $row['assetId'];
            $code = $row['code'];
            $asset = $this->assetRepository->find($assetId);
            $assets[$code] = $asset;
        }

        return $assets;
    }
}