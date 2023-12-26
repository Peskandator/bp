<?php

namespace App\Odpisy\Action;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Odpisy\Components\CancelDepreciationExecutionResolver;
use Doctrine\ORM\EntityManagerInterface;

class CancelDepreciationExecutionAction
{
    protected EntityManagerInterface $entityManager;
    private CancelDepreciationExecutionResolver $cancelDepreciationExecutionResolver;

    public function __construct(
        EntityManagerInterface $entityManager,
        CancelDepreciationExecutionResolver $cancelDepreciationExecutionResolver,
    ) {
        $this->entityManager = $entityManager;
        $this->cancelDepreciationExecutionResolver = $cancelDepreciationExecutionResolver;
    }

    public function __invoke(AccountingEntity $entity, int $year): void
    {
        $taxDepreciations = $entity->getExecutedTaxDepreciationsForYear($year);
        $accountingDepreciations = $entity->getExecutedAccountingDepreciationsForYear($year);

        /**
         * @var DepreciationTax $taxDepreciation
         */
        foreach ($taxDepreciations as $taxDepreciation) {
            if (!$this->cancelDepreciationExecutionResolver->isTaxDepreciationCancelable($taxDepreciation)) {
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
            if (!$this->cancelDepreciationExecutionResolver->isAccountingDepreciationCancelable($accountingDepreciation)) {
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
