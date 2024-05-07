<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Presenters\BaseAccountingEntityPresenter;

final class AccountingPresenter extends BaseAccountingEntityPresenter
{

    public function __construct(
    )
    {
        parent::__construct();
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

//        $this->template->movements = $this->currentEntity->getExecutedAccountingDepreciationsForYear($selectedYear);

    }
}