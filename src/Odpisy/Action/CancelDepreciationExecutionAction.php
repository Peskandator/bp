<?php

namespace App\Odpisy\Action;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use Doctrine\ORM\EntityManagerInterface;

class CancelDepreciationExecutionAction
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, int $year): void
    {
        $taxDepreciations = $entity->getExecutedTaxDepreciationsForYear($year);
        $accountingDepreciations = $entity->getExecutedAccountingDepreciationsForYear($year);

        /**
         * @var DepreciationTax $taxDepreciation
         */
        foreach ($taxDepreciations as $taxDepreciation) {
            if (!$taxDepreciation->isExecutionCancelable()) {
                continue;
            }
            $asset = $taxDepreciation->getAsset();
            $movementTax = $asset->getDepreciationTaxExecutionMovement($taxDepreciation);
            $taxDepreciation->setExecuted(false);
            $this->entityManager->remove($movementTax);
        }
        /**
         * @var DepreciationAccounting $accountingDepreciation
         */
        foreach ($accountingDepreciations as $accountingDepreciation) {
            if (!$accountingDepreciation->isExecutionCancelable()) {
                continue;
            }
            $asset = $accountingDepreciation->getAsset();
            $movementAccounting = $asset->getDepreciationAccountingExecutionMovement($accountingDepreciation);
            $accountingDepreciation->setExecuted(false);
            $this->entityManager->remove($movementAccounting);
        }

        $this->entityManager->flush();
    }
}
