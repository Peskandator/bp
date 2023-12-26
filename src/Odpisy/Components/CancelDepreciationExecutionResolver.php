<?php
declare(strict_types=1);

namespace App\Odpisy\Components;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;

class CancelDepreciationExecutionResolver
{
    public function __construct(
    )
    {
    }


    public function isTaxDepreciationCancelable(DepreciationTax $depreciationTax): bool
    {
        $asset = $depreciationTax->getAsset();
        $year = $depreciationTax->getYear();
        $nextYearDepreciation = $asset->getTaxDepreciationForYear($year + 1);
        if ($nextYearDepreciation !== null && $nextYearDepreciation->isExecuted()) {
            return false;
        }

        return true;
    }

    public function isAccountingDepreciationCancelable(DepreciationAccounting $depreciationAccounting): bool
    {
        $asset = $depreciationAccounting->getAsset();
        $year = $depreciationAccounting->getYear();
        $nextYearDepreciation = $asset->getAccountingDepreciationForYear($year + 1);
        if ($nextYearDepreciation !== null && $nextYearDepreciation->isExecuted()) {
            return false;
        }

        return true;
    }

    public function areCancellableExecutedDepreciationsForYearExisting(AccountingEntity $entity, int $year): bool
    {
        $taxDepreciations = $entity->getExecutedTaxDepreciationsForYear($year);
        $accountingDepreciations = $entity->getExecutedAccountingDepreciationsForYear($year);

        /**
         * @var DepreciationTax $taxDepreciation
         */
        foreach ($taxDepreciations as $taxDepreciation) {
            if ($this->isTaxDepreciationCancelable($taxDepreciation)) {
                return true;
            }
        }
        /**
         * @var DepreciationAccounting $accountingDepreciation
         */
        foreach ($accountingDepreciations as $accountingDepreciation) {
            if ($this->isAccountingDepreciationCancelable($accountingDepreciation)) {
                return true;
            }
        }

        return false;
    }
}
