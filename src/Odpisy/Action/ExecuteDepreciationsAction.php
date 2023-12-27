<?php

namespace App\Odpisy\Action;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Majetek\Components\MovementGenerator;
use Doctrine\ORM\EntityManagerInterface;

class ExecuteDepreciationsAction
{
    protected EntityManagerInterface $entityManager;
    private MovementGenerator $movementGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        MovementGenerator $movementGenerator,
    ) {
        $this->entityManager = $entityManager;
        $this->movementGenerator = $movementGenerator;
    }

    public function __invoke(AccountingEntity $entity, array $data, \DateTimeInterface $executionDate): void
    {
        foreach ($data as $assetId => $content) {
            /**
             * @var DepreciationTax $depreciationTax
             */
            $depreciationTax = $content["tax"] ?? null;
            /**
             * @var DepreciationAccounting $depreciationAccounting
             */
            $depreciationAccounting = $content["accounting"] ?? null;

            if ($depreciationTax) {
                $depreciationTax->setExecuted(true);
                $movement = $this->movementGenerator->createMovementFromDepreciation($depreciationTax, false, $executionDate);
                $movement->setTaxDepreciation($depreciationTax);
            }
            if ($depreciationAccounting) {
                $depreciationAccounting->setExecuted(true);
                $movement = $this->movementGenerator->createMovementFromDepreciation($depreciationAccounting, true, $executionDate);
                $movement->setAccountingDepreciation($depreciationAccounting);
            }
        }
        $this->entityManager->flush();
    }


}
