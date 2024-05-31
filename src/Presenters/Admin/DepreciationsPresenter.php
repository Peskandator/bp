<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Utils\PhpXlsxGenerator;
use Nette\Application\UI\Form;

final class DepreciationsPresenter extends BaseAccountingEntityPresenter
{
    private EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory;
    private EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory,
        EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory,
        EditDepreciationCalculator $editDepreciationCalculator,
    )
    {
        parent::__construct();
        $this->editTaxDepreciationFormFactory = $editTaxDepreciationFormFactory;
        $this->editAccountingDepreciationFormFactory = $editAccountingDepreciationFormFactory;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function actionDefault(?int $yearArg = null, string $type = "tax"): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Odpisy',
                null)
        );

        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }
        $viewAccounting = false;
        if ($type === "accounting") {
            $viewAccounting = true;
        }

        $this->template->taxDepreciations = $this->currentEntity->getTaxDepreciationsForYear($year);
        $this->template->accountingDepreciations = $this->currentEntity->getAccountingDepreciationsForYear($year);
        $this->template->availableYears = $this->currentEntity->getAvailableYears();
        $this->template->selectedYear = $year;
        $this->template->viewAccounting = $viewAccounting;
        $this->template->editDepreciationCalculator = $this->editDepreciationCalculator;
    }

    protected function createComponentEditTaxDepreciationForm(): Form
    {
        $form = $this->editTaxDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentEditAccountingDepreciationForm(): Form
    {
        $form = $this->editAccountingDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    public function actionExport(?int $yearArg = null, string $type = "tax"): void
    {
        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }

        $depreciations = $this->currentEntity->getTaxDepreciationsForYear($year);
        $fileName = $this->currentEntity->getName() . ' - Daňové odpisy ' . $year . '.xlsx';
        if ($type === "accounting") {
            $depreciations = $this->currentEntity->getAccountingDepreciationsForYear($year);
            $fileName = $this->currentEntity->getName() . ' - Účetní odpisy ' . $year . '.xlsx';
        }


        $firstRow = [
            'Majetek',
            'Typ',
            'Odp.sk., způsob',
            'Rok odpisu',
            'VC',
            'Zvýš. VC',
            'Sazba',
            '%',
            'Odpis',
            'Oprávky',
            'ZC',
            'Provést',
            'Provedeno',
        ];

        $rows = $this->getDataForExport($depreciations);
        $xlsxFile = PhpXlsxGenerator::fromArray($firstRow, $rows);
        $xlsxFile->downloadAs($fileName);
    }

    protected function getDataForExport(array $depreciations): array
    {
        $rows = [];

        foreach ($depreciations as $depreciation) {
            $asset = $depreciation->getAsset();
            $taxGroup = $asset->getDepreciationGroupTax();
            $taxGroupName = $taxGroup ? $taxGroup->getFullName() : '';

            $row = [];
            $row[] = $asset->getName();
            $row[] = $asset->getAssetType()->getName();
            $row[] = $taxGroupName;
            $row[] = $depreciation->getDepreciationYear();
            $row[] = $depreciation->getEntryPrice();
            $row[] = $depreciation->getIncreasedEntryPrice();
            $row[] = $depreciation->getRate();
            $row[] = $depreciation->getPercentage();
            $row[] = $depreciation->getDepreciationAmount();
            $row[] = $depreciation->getDepreciatedAmount();
            $row[] = $depreciation->getResidualPrice();
            $row[] = $depreciation->isExecutable() ? 'ANO' : 'NE';
            $row[] = $depreciation->isExecuted() ? 'ANO' : 'NE';

            $rows[] = $row;
        }

        return $rows;
    }
}