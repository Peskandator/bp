<?php

namespace App\Odpisy\Action;

use App\Entity\AccountingEntity;
use App\Entity\Depreciation;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;
use App\Entity\Movement;
use App\Majetek\Enums\MovementType;
use App\Majetek\Requests\CreateMovementRequest;
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
                $movement = $this->createMovementFromDepreciation($depreciationTax, false);
                $movement->setTaxDepreciation($depreciationTax);
            }
            if ($depreciationAccounting) {
                $depreciationAccounting->setExecuted(true);
                $movement = $this->createMovementFromDepreciation($depreciationAccounting, true);
                $movement->setAccountingDepreciation($depreciationAccounting);
            }
        }
        $this->entityManager->flush();
    }

    protected function createMovementFromDepreciation(Depreciation $depreciation, bool $isAccounting): Movement
    {
        $asset = $depreciation->getAsset();
        $type = $isAccounting ? MovementType::DEPRECIATION_ACCOUNTING : MovementType::DEPRECIATION_TAX;
        $category = $asset->getCategory();

        $request = new CreateMovementRequest(
            $asset,
            $type,
            $depreciation->getDepreciationAmount(),
            $depreciation->getResidualPrice(),
            "",
            $category->getAccountDepreciation(),
            $category->getAccountRepairs(),
        );

        $movement = new Movement($request);
        $this->entityManager->persist($movement);
        $asset->getMovements()->add($movement);

        return $movement;
    }
}
