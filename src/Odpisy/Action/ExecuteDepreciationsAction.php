<?php

namespace App\Odpisy\Action;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use Doctrine\ORM\EntityManagerInterface;

class ExecuteDepreciationsAction
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, array $data): void
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
            }
            if ($depreciationAccounting) {
                $depreciationAccounting->setExecuted(true);
            }
        }
        $this->entityManager->flush();
    }
}
