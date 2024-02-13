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

    public function areCancellableExecutedDepreciationsForYearExisting(AccountingEntity $entity, int $year): bool
    {
        $taxDepreciations = $entity->getExecutedTaxDepreciationsForYear($year);
        $accountingDepreciations = $entity->getExecutedAccountingDepreciationsForYear($year);

        /**
         * @var DepreciationTax $taxDepreciation
         */
        foreach ($taxDepreciations as $taxDepreciation) {
            if ($taxDepreciation->isExecutionCancelable()) {
                return true;
            }
        }
        /**
         * @var DepreciationAccounting $accountingDepreciation
         */
        foreach ($accountingDepreciations as $accountingDepreciation) {
            if ($accountingDepreciation->isExecutionCancelable()) {
                return true;
            }
        }

        return false;
    }
}
