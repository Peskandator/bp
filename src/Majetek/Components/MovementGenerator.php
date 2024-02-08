<?php
declare(strict_types=1);

namespace App\Majetek\Components;

use App\Entity\Asset;
use App\Entity\Depreciation;
use App\Entity\Movement;
use App\Majetek\Enums\MovementType;
use App\Majetek\Requests\CreateMovementRequest;
use Doctrine\ORM\EntityManagerInterface;

class MovementGenerator
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function createInclusionMovement(Asset $asset): void
    {
        $movementRequest = $this->createInclusionMovementRequest($asset);
        $movement = new Movement($movementRequest);
        $this->entityManager->persist($movement);
        $asset->getMovements()->add($movement);
    }

    protected function createInclusionMovementRequest(Asset $asset): CreateMovementRequest
    {
        return new CreateMovementRequest(
            $asset,
            MovementType::INCLUSION,
            $asset->getEntryPrice(),
            $asset->getEntryPrice(),
            "",
            "účet MD zařazení",
            "321000",
            $asset->getEntryDate(),
        );
    }

    public function createDisposalMovement(Asset $asset): void
    {
        $movementRequest = $this->createDisposalMovementRequest($asset);
        $movement = new Movement($movementRequest);
        $this->entityManager->persist($movement);
        $asset->getMovements()->add($movement);
    }

    protected function createDisposalMovementRequest(Asset $asset): CreateMovementRequest
    {
        $category = $asset->getCategory();

        return new CreateMovementRequest(
            $asset,
            MovementType::DISPOSAL,
            $asset->getAmortisedPriceAccounting(),
            0,
            "",
            $category->getAccountDepreciation(),
            $category->getAccountRepairs(),
            $asset->getDisposalDate(),
        );
    }

    public function createMovementFromDepreciation(Depreciation $depreciation, bool $isAccounting, \DateTimeInterface $executionDate): Movement
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
            $executionDate,
        );

        $movement = new Movement($request);
        $this->entityManager->persist($movement);
        $asset->getMovements()->add($movement);

        return $movement;
    }

    public function regenerateMovementsAfterAssetEdit(Asset $asset): void
    {
        $inclusionMovement = $asset->getInclusionMovement();
        $disposalMovement = $asset->getDisposalMovement();

        if (!$asset->isIncluded()) {
            $this->removeMovement($inclusionMovement);
            $this->removeMovement($disposalMovement);
            return;
        }
        if ($inclusionMovement) {
            $inclusionMovement->update($this->createInclusionMovementRequest($asset));
        } else  {
            $this->createInclusionMovement($asset);
        }
        if ($asset->getDisposalDate()) {
            if ($disposalMovement) {
                $disposalMovement->update($this->createDisposalMovementRequest($asset));
                return;
            }
            $this->createDisposalMovement($asset);
            return;
        }
        $this->removeMovement($disposalMovement);
    }

    protected function removeMovement(?Movement $movement): void
    {
        if ($movement) {
            $this->entityManager->remove($movement);
        }
    }
}
