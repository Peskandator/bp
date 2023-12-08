<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Acquisition;
use App\Entity\Asset;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\Disposal;
use App\Entity\Location;
use App\Entity\Place;

class DeletabilityResolver
{

    public function __construct(
    ) {
    }

    public function isCategoryDeletable(Category $category): bool
    {
        $entity = $category->getEntity();
        $assets = $entity->getAssets();
        $categoryId = $category->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetCategory = $asset->getCategory();
            if ($assetCategory && $assetCategory->getId() === $categoryId) {
                return false;
            }
        }
        return true;
    }

    public function isDepreciationGroupDeletable(DepreciationGroup $group): bool
    {
        $entity = $group->getEntity();
        $assets = $entity->getAssets();
        $groupId = $group->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetGroupTax = $asset->getDepreciationGroupTax();
            if ($assetGroupTax && $assetGroupTax->getId() === $groupId) {
                return false;
            }
            $assetGroupAccounting = $asset->getDepreciationGroupAccounting();
            if ($assetGroupAccounting && $assetGroupAccounting->getId() === $groupId) {
                return false;
            }
        }
        $categories = $entity->getCategories();
        /**
         * @var Category $category
         */
        foreach ($categories as $category) {
            $categoryGroup = $category->getDepreciationGroup();
            if ($categoryGroup && $categoryGroup->getId() === $groupId) {
                return false;
            }
        }

        return true;
    }

    public function isPlaceDeletable(Place $place): bool
    {
        $entity = $place->getEntity();
        $assets = $entity->getAssets();
        $placeId = $place->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetPlace = $asset->getPlace();
            if ($assetPlace && $assetPlace->getId() === $placeId) {
                return false;
            }
        }
        return true;
    }

    public function isLocationDeletable(Location $location): bool
    {
        $entity = $location->getEntity();
        $assets = $entity->getAssets();
        $locationId = $location->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetLocation = $asset->getLocation();
            if ($assetLocation && $assetLocation->getId() === $locationId) {
                return false;
            }
        }
        $places = $entity->getPlaces();
        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            $placeLocation = $place->getLocation();
            if ($placeLocation && $placeLocation->getId() === $locationId) {
                return false;
            }
        }
        return true;
    }

    public function isAcquisitionDeletable(Acquisition $acquisition): bool
    {
        if ($acquisition->isDefault()) {
            return false;
        }
        $entity = $acquisition->getEntity();
        if (!$entity) {
            return false;
        }
        $assets = $entity->getAssets();
        $acquisitionId = $acquisition->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetAcquisition = $asset->getAcquisition();
            if ($assetAcquisition && $assetAcquisition->getId() === $acquisitionId) {
                return false;
            }
        }
        return true;
    }

    public function isDisposalDeletable(Disposal $disposal): bool
    {
        $entity = $disposal->getEntity();
        if ($disposal->isDefault()) {
            return false;
        }
        $assets = $entity->getAssets();
        $disposalId = $disposal->getId();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            $assetDisposal = $asset->getDisposal();
            if ($assetDisposal && $assetDisposal->getId() === $disposalId) {
                return false;
            }
        }
        return true;
    }
}
