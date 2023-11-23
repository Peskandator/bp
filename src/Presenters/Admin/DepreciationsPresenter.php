<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Presenters\BaseAdminPresenter;

final class DepreciationsPresenter extends BaseAdminPresenter
{
    public function __construct(
    )
    {
        parent::__construct();
    }

    public function actionDefault(?int $yearArg = null): void
    {
        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }

        $this->template->taxDepreciations = $this->getTaxDepreciationsForYear($year);
        $this->template->accountingDepreciations = $this->getAccountingDepreciationsForYear($year);
        $this->template->availableYears = $this->getAvailableYears();
        $this->template->selectedYear = $year;
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
}