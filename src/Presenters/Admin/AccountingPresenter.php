<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Odpisy\Components\DepreciationsAccountingDataGenerator;
use App\Presenters\BaseAccountingEntityPresenter;

final class AccountingPresenter extends BaseAccountingEntityPresenter
{

    private DepreciationsAccountingDataGenerator $accountingDataGenerator;

    public function __construct(
        DepreciationsAccountingDataGenerator $accountingDataGenerator,
    )
    {
        parent::__construct();
        $this->accountingDataGenerator = $accountingDataGenerator;
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

        // UPDATOVAT změny - jen id movementů, které nejsou v poli
        // + tlačítko přegenerovat
//        if ($accountingDataForYear) {
//            $data = $accountingDataForYear->getArrayData();
//        } else {
            $data = $this->accountingDataGenerator->createDepreciationsAccountingData($this->currentEntity, $selectedYear);
//        }

        bdump($data);

        $this->template->data = $data;
    }
}