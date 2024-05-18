<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Majetek\ORM\MovementRepository;
use App\Odpisy\Action\RegenerateDepreciationsAccountingDataAction;
use App\Odpisy\Components\DepreciationsAccountingDataGenerator;
use App\Odpisy\Forms\EditDepreciationsAccountingDataFormFactory;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class AccountingPresenter extends BaseAccountingEntityPresenter
{
    private DepreciationsAccountingDataGenerator $accountingDataGenerator;
    private MovementRepository $movementRepository;
    private EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory;
    private RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction;

    public function __construct(
        DepreciationsAccountingDataGenerator $accountingDataGenerator,
        MovementRepository $movementRepository,
        EditDepreciationsAccountingDataFormFactory $editAccountingDataFormFactory,
        RegenerateDepreciationsAccountingDataAction $regenerateDepreciationsAccountingDataAction,
    )
    {
        parent::__construct();
        $this->accountingDataGenerator = $accountingDataGenerator;
        $this->movementRepository = $movementRepository;
        $this->editAccountingDataFormFactory = $editAccountingDataFormFactory;
        $this->regenerateDepreciationsAccountingDataAction = $regenerateDepreciationsAccountingDataAction;
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

    protected function createComponentEditDepreciationsAccountingData(): Form
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
}