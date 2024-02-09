<?php
declare(strict_types=1);

namespace App\Majetek\Components;

use App\Entity\Asset;
use App\Entity\Depreciation;
use App\Entity\Movement;
use App\Majetek\Enums\MovementType;
use App\Majetek\Requests\CreateAssetRequest;
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
        $this->createMovement($asset, $movementRequest);
    }

    protected function createInclusionMovementRequest(Asset $asset): CreateMovementRequest
    {
        return new CreateMovementRequest(
            $asset,
            MovementType::INCLUSION,
            $asset->getEntryPrice(),
            $asset->getEntryPrice(),
            "Zařazení majetku",
            "účet MD zařazení",
            "321000",
            $asset->getEntryDate(),
        );
    }

    public function createDisposalMovement(Asset $asset): void
    {
        $movementRequest = $this->createDisposalMovementRequest($asset);
        $this->createMovement($asset, $movementRequest);
    }

    protected function createDisposalMovementRequest(Asset $asset): CreateMovementRequest
    {
        $category = $asset->getCategory();

        return new CreateMovementRequest(
            $asset,
            MovementType::DISPOSAL,
            $asset->getAmortisedPriceAccounting(),
            0,
            "Vyřazení majetku",
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
            "Provedení odpisu",
            $category->getAccountDepreciation(),
            $category->getAccountRepairs(),
            $executionDate,
        );

        return $this->createMovement($asset, $request);
    }

    public function createEntryPriceChangeMovement(?Asset $asset, CreateAssetRequest $request, bool $editing): void
    {
        $correctEntryPrice = $request->entryPrice;
        if ($editing) {
            $correctEntryPrice = $asset->getCorrectEntryPrice();
        }
        $value = $request->increasedEntryPrice - $correctEntryPrice;

        $description = "Zvýšení vstupní ceny";
        if ($value < 0) {
            $description = "Snížení vstupní ceny";
        }

        $movementRequest = new CreateMovementRequest(
            $asset,
            MovementType::ENTRY_PRICE_CHANGE,
            $value,
            $correctEntryPrice,
            $description,
            "042000",
            "321000",
            $request->increaseDate,
        );
        $this->createMovement($asset, $movementRequest);
    }

    protected function createLocationChangeMovement(Asset $asset, CreateAssetRequest $request): void
    {
        $desription = "Změna střediska z ";
        $name1 = $asset->getLocation() ? $asset->getLocation()->getName() : "žádného";
        $desription .= $name1;
        // TODO vyzkoušet
        $name2 = $request->place ? ($request->place->getLocation() ? $request->place->getLocation()->getName() : "žádné") : "žádné";
        $desription .= " na " . $name2;

        $movementRequest = new CreateMovementRequest(
            $asset,
            MovementType::LOCATION_CHANGE,
            0,
            0,
            $desription,
            "",
            "",
            new \DateTimeImmutable("now"),
        );
        $this->createMovement($asset, $movementRequest);
    }

    protected function createPlaceChangeMovement(Asset $asset, CreateAssetRequest $request): void
    {
        $desription = "Změna místa z ";
        $name1 = $asset->getPlace() ? $asset->getPlace()->getName() : "žádného";
        $desription .= $name1;
        $name2 = $request->place ? $request->place->getName() : "žádné";
        $desription .= " na " . $name2;

        $movementRequest = new CreateMovementRequest(
            $asset,
            MovementType::PLACE_CHANGE,
            0,
            0,
            $desription,
            "",
            "",
            new \DateTimeImmutable("now"),
        );
        $this->createMovement($asset, $movementRequest);
    }

    protected function createMovement(Asset $asset, CreateMovementRequest $request): Movement
    {
        $movement = new Movement($request);
        $this->entityManager->persist($movement);
        $asset->getMovements()->add($movement);
        return $movement;
    }

    public function regenerateMovementsAfterAssetEdit(Asset $asset): void
    {
        $inclusionMovement = $asset->getInclusionMovement();
        $disposalMovement = $asset->getDisposalMovement();

        // this code is partially not needed in current app state
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

    public function generateEntryPriceChangeMovement(Asset $asset, CreateAssetRequest $request): void
    {
        if ($asset->getIncreasedEntryPrice() !== $request->increasedEntryPrice) {
            $this->createEntryPriceChangeMovement($asset, $request, true);
        }
    }

    public function generateInfoChangeMovements(Asset $asset, CreateAssetRequest $request): void
    {
        if ($this->getLocationIdFromRequest($request) !== $this->getLocationIdAsset($asset)) {
            $this->createLocationChangeMovement($asset, $request);
        }
        if ($this->getPlaceIdFromRequest($request) !== $this->getPlaceIdAsset($asset)) {
            $this->createPlaceChangeMovement($asset, $request);
        }
    }

    protected function getLocationIdFromRequest(CreateAssetRequest $request): ?int
    {
        if ($request->place === null) {
            return null;
        }
        return $request->place->getLocation()->getId();
    }

    protected function getLocationIdAsset(Asset $asset): ?int
    {
        $location = $asset->getLocation();
        if ($asset->getLocation() === null) {
            return null;
        }
        return $location->getId();
    }

    protected function getPlaceIdFromRequest(CreateAssetRequest $request): ?int
    {
        $place = $request->place;
        if ($place === null) {
            return null;
        }
        return $place->getId();
    }

    protected function getPlaceIdAsset(Asset $asset): ?int
    {
        $place = $asset->getPlace();
        if ($asset->getPlace() === null) {
            return null;
        }
        return $place->getId();
    }

    protected function removeMovement(?Movement $movement): void
    {
        if ($movement) {
            $this->entityManager->remove($movement);
        }
    }
}