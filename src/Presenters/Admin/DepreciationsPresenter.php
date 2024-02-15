<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAdminPresenter;
use Nette\Application\UI\Form;

final class DepreciationsPresenter extends BaseAdminPresenter
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
}