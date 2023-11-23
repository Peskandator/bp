<?php

declare(strict_types=1);

namespace App\Majetek\Components;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Asset;
use App\Entity\AssetType;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\Place;

class AssetFormJsonGenerator
{

    public function __construct(
    ) {
    }

    public function createCategoriesGroupsJson(AccountingEntity $entity): string
    {
        $jsonArr = [];
        $categories =$entity->getCategories();

        /**
         * @var Category $category
         */
        foreach ($categories as $category) {
            $group =  $category->getDepreciationGroup();
            if ($group) {
                $jsonArr[(string)$category->getId()] = $group->getId();
                continue;
            }
            $jsonArr[$category->getId()] = '';
        }

        return json_encode($jsonArr);
    }

    public function createPlacesLocationsJson(AccountingEntity $entity): string
    {
        $jsonArr = [];
        $places = $entity->getPlaces();

        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            $locationId = $place->getLocation()->getId();
            $jsonArr[(string)$place->getId()] = $locationId;
        }

        return json_encode($jsonArr);
    }

    public function getNextNumberForAssetTypesJson(AccountingEntity $entity, array $assetTypes, ?Asset $asset): string
    {
        $currentAssetType = $asset?->getAssetType();

        $nextNumbers = [];
        /**
         * @var AssetType $assetType
         */
        foreach ($assetTypes as $assetType) {
            if ($currentAssetType && $assetType->getId() === $currentAssetType->getId()) {
                $nextNumbers[(string)$assetType->getId()] = $asset->getInventoryNumber();
                continue;
            }

            $series = $assetType->getSeries();
            $step = $assetType->getStep();
            $numberFound = false;

            $counter = 0;

            while ($numberFound === false) {
                $newSeriesNumber = $series + $step * $counter;
                if (!$this->isInventoryNumberAvailable($entity, $newSeriesNumber)) {
                    $counter++;
                    continue;
                }
                $nextNumbers[(string)$assetType->getId()] = $newSeriesNumber;
                $numberFound = true;
            }
        }

        return json_encode($nextNumbers);
    }

    public function getAssetTypeCodesJson(AccountingEntity $entity, array $assetTypes): string
    {
        $assetTypeCodes = [];
        /**
         * @var AssetType $assetType
         */
        foreach ($assetTypes as $assetType) {
            $assetTypeCodes[(string)$assetType->getId()] = $assetType->getCode();
        }

        return json_encode($assetTypeCodes);
    }

    public function isInventoryNumberAvailable(AccountingEntity $entity, int $number): bool
    {
        $assets = $entity->getAssets();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            if ($asset->getInventoryNumber() === $number) {
                return false;
            }
        }
        return true;
    }

    public function getAcquisitionCodesJson(AccountingEntity $entity): string
    {
        $codes = [];
        $acquisitions = $entity->getAcquisitions();

        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            $codes[(string)$acquisition->getId()] = $acquisition->getCode();
        }

        return json_encode($codes);
    }

    public function getGroupsInfoJson(AccountingEntity $entity): string
    {
        $info = [];
        $groups = $entity->getDepreciationGroupsWithoutAccounting();
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            $info[(string)$group->getId()]['rate-first'] = $group->getRateFirstYear();
            $info[(string)$group->getId()]['rate'] = $group->getRate();
            $info[(string)$group->getId()]['rate-increased'] = $group->getRateIncreasedPrice();
            $info[(string)$group->getId()]['years'] = $group->getYears();
            $info[(string)$group->getId()]['months'] = $group->getMonths();
            $info[(string)$group->getId()]['coeff'] = $group->isCoefficient() ? 1 : 0;
        }

        return json_encode($info);
    }


}
