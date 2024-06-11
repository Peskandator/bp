<?php
declare(strict_types=1);

namespace App\Majetek\Components;


use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\ORM\AssetRepository;
use App\Utils\DateTimeFormatter;
use App\Utils\EnumerableSorter;

class AssetReportsFilter
{
    private DateTimeFormatter $dateTimeFormatter;
    private AccountingEntity $currentEntity;
    private AssetRepository $assetRepository;
    private EnumerableSorter $enumerableSorter;

    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        AssetRepository $assetRepository,
        EnumerableSorter $enumerableSorter,
    )
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->assetRepository = $assetRepository;
        $this->enumerableSorter = $enumerableSorter;
    }

    public function getResults(AccountingEntity $entity, string $filter): array
    {
        $this->currentEntity = $entity;

        $filterDataStdClass = json_decode(urldecode($filter));

        $filterData =  json_decode(json_encode($filterDataStdClass), true);

        $filteredAssets = $this->getFilteredResults($filterData);
        $sortedAssets = $this->sortAssets($filterData, $filteredAssets);
        $groupedAssets = $this->groupAssets($filterData, $sortedAssets);

        return $groupedAssets;
    }

    protected function getFilteredResults(?array $filter): array
    {
        $assets = $this->assetRepository->findAll();

        $result = [];

        $allowedTypes = $filter['entry_price_from'] ?? null;
        $allowedCategories = $filter['entry_price_from'] ?? null;
        $allowedPlaces = $filter['entry_price_from'] ?? null;
        $fromPrice = $filter['entry_price_from'] ?? null;
        $toPrice = $filter['entry_price_to'] ?? null;
        $withDisposed = $filter['disposed'] ?? null;

        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            if ($this->currentEntity->getId() !== $asset->getEntity()->getId()) {
                continue;
            }

            if ($asset->isDisposed() && !$withDisposed) {
                continue;
            }

            if ($allowedTypes && count($allowedTypes) > 0 && !in_array($asset->getAssetType()->getId(), $allowedTypes)) {
                continue;
            }

            if ($allowedCategories && count($allowedCategories) > 0 && !in_array($asset->getCategory()->getId(), $allowedCategories)) {
                continue;
            }

            if ($allowedPlaces && count($allowedPlaces) > 0 && !in_array($asset->getPlace()->getId(), $allowedPlaces)) {
                continue;
            }

            $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($fromDate && $asset->getEntryDate() < $fromDate) {
                continue;
            }
            $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($toDate && $asset->getEntryDate() > $toDate) {
                continue;
            }

            $currentYear = (int)date("Y");
            if ($fromPrice !== null && $asset->getPriceForYear($currentYear) < $fromPrice) {
                continue;
            }
            if ($toPrice !== null && $asset->getPriceForYear($currentYear) > $toPrice) {
                continue;
            }

            $result[] = $asset;
        }

        return $result;
    }

    protected function sortAssets(?array $filter, array $records): array
    {
        $sortBy = $filter['sorting'];
        $assets = $records;

        if ($sortBy !== null) {
            usort($assets, function (Asset $first, Asset $second) use ($sortBy) {
                $firstValue = $this->getSortByColumnValue($first, $sortBy);
                $secondValue = $this->getSortByColumnValue($second, $sortBy);
                if ($firstValue > $secondValue) {
                    return 1;
                }
                if ($firstValue < $secondValue) {
                    return -1;
                };
                return 0;
            });
        }

        return $assets;
    }

    protected function getSortByColumnValue(Asset $asset, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
            case 'entry_date':
                return $asset->getEntryDate();
            case 'name':
                return $asset->getName();
            case 'entry_price':
                return $asset->getEntryPrice();
            case 'increased_price':
                return $asset->getIncreasedEntryPrice();
            case 'depreciated_amount_tax':
                return $asset->getDepreciatedAmountTax();
            case 'residual_price_tax':
                return $asset->getAmortisedPriceTax();
            case 'depreciated_amount_accounting':
                return $asset->getDepreciatedAmountAccounting();
            case 'residual_price_accounting':
                return $asset->getAmortisedPriceAccounting();
            default:
                return $asset->getInventoryNumber();
        }
    }

    protected function groupAssets(?array $filter, array $records): array
    {
        $groupBy = $filter['grouping'];

        $assets = [];

        if ($groupBy !== null || $groupBy !== 'none') {
            foreach ($records as $record) {
                $groupByValue = $this->getGroupByColumnValue($record, $groupBy);
                $assets[$groupByValue][] = $record;
            }

            if ($groupBy === 'depreciation_group_tax' || $groupBy === 'depreciation_group_accounting') {
                $assets = $this->enumerableSorter->sortGroupsInArrayByMethodAndNumber($assets);
            }

            return $assets;
        }
        $assets['none'] = $records;
        return $assets;
    }

    protected function getGroupByColumnValue(Asset $asset, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
            case 'type':
                return $asset->getAssetType()->getName();
            case 'category':
                return $asset->getCategory() ? $asset->getCategory()->getName() : 'Bez zařazení';
            case 'depreciation_group_accounting':
                return $asset->getCorrectDepreciationGroupAccounting()->getFullName();
            case 'depreciation_group_tax':
                $groupTax = $asset->getDepreciationGroupTax();
                return $groupTax ? $groupTax->getFullName() : 'Bez zařazení';
            case 'entry_date':
                return $asset->getEntryDate()->format('Y');
            default:
                return 'all';
        }
    }
}