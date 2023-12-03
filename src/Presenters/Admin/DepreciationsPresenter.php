<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAdminPresenter;
use Nette\Application\UI\Form;

final class DepreciationsPresenter extends BaseAdminPresenter
{
    private EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory;
    private EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory;

    public function __construct(
        EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory,
        EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory,
    )
    {
        $this->editTaxDepreciationFormFactory = $editTaxDepreciationFormFactory;
        parent::__construct();
        $this->editAccountingDepreciationFormFactory = $editAccountingDepreciationFormFactory;
    }

    public function actionDefault(?int $yearArg = null, string $type = "tax"): void
    {
        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }
        $viewAccounting = false;
        if ($type === "accounting") {
            $viewAccounting = true;
        }

        $this->template->taxDepreciations = $this->getTaxDepreciationsForYear($year);
        $this->template->accountingDepreciations = $this->getAccountingDepreciationsForYear($year);
        $this->template->availableYears = $this->getAvailableYears();
        $this->template->selectedYear = $year;
        $this->template->viewAccounting = $viewAccounting;
    }

    protected function getTaxDepreciationsForYear(int $year): array
    {
        $matched = [];
        $depreciations = $this->currentEntity->getTaxDepreciations();

        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($depreciations as $depreciation) {
            if ($depreciation->getYear() === $year) {
                $matched[] = $depreciation;
            }
        }

        return $matched;
    }

    protected function getAccountingDepreciationsForYear(int $year): array
    {
        $matched = [];
        $depreciations = $this->currentEntity->getAccountingDepreciations();

        /**
         * @var DepreciationAccounting $depreciation
         */
        foreach ($depreciations as $depreciation) {
            if ($depreciation->getYear() === $year) {
                $matched[] = $depreciation;
            }
        }

        return $matched;
    }

    protected function getAvailableYears(): array
    {
        $availableYears = [];

        $depreciations = $this->currentEntity->getTaxDepreciations();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($depreciations as $depreciation) {
            $depreciationYear = $depreciation->getYear();
            if (!in_array($depreciationYear, $availableYears)) {
                $availableYears[] = $depreciationYear;
            }
        }

        return $availableYears;
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
}