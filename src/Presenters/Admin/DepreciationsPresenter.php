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

    public function actionDefault(?int $yearArg): void
    {
        $year = $yearArg;
        if (!$year) {
            $today = new \DateTimeImmutable('today');
            $year = (int)$today->format('Y');
        }

        $this->template->depreciationsTax = $this->getTaxDepreciationsForYear($year);
        $this->template->depreciationsAccounting = $this->getAccountingDepreciationsForYear($year);
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

}