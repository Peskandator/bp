<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Reports\Enums\AssetColumns;
use App\Utils\DateTimeFormatter;
use App\Utils\EnumerableSorter;

class AssetReportsFilter
{
    private DateTimeFormatter $dateTimeFormatter;
    private AccountingEntity $currentEntity;
    private EnumerableSorter $enumerableSorter;

    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        EnumerableSorter $enumerableSorter,
    )
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->enumerableSorter = $enumerableSorter;
    }

    public function getResults(AccountingEntity $entity, ?array $filter): array
    {
        $this->currentEntity = $entity;

        $filteredAssets = $this->getFilteredResults($filter);
        $sortedAssets = $this->sortAssets($filter, $filteredAssets);
        $groupedAssets = $this->groupAssets($filter, $sortedAssets);
        $data = $this->createDataForExport($filter, $groupedAssets);

        return $data;
    }

    protected function getFilteredResults(?array $filter): array
    {
        $assets = $this->currentEntity->getAssetsSorted();

        $result = [];

        $allowedTypes = $filter['types'] ?? null;
        $allowedCategories = $filter['categories'] ?? null;
        $allowedPlaces = $filter['places'] ?? null;
        $fromPrice = $filter['entry_price_from'] ?? null;
        $toPrice = $filter['entry_price_to'] ?? null;
        $withDisposed = $filter['disposed'] ?? null;
        $fromAccount = $filter['account_from'] ?? null;
        $toAccount = $filter['account_to'] ?? null;

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

            if ($asset->getCategory() && $allowedCategories && count($allowedCategories) > 0 && !in_array($asset->getCategory()->getId(), $allowedCategories)) {
                continue;
            }

            if ($asset->getPlace() && $allowedPlaces && count($allowedPlaces) > 0 && !in_array($asset->getPlace()->getId(), $allowedPlaces)) {
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

            $category = $asset->getCategory();
            $assetAccount = $category?->getAccountAsset();
            if ($fromAccount && $assetAccount < $fromAccount) {
                continue;
            }
            if ($toAccount && $assetAccount > $toAccount) {
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
            case 'account':
                $category = $asset->getCategory();
                return $category ? ($category->getAccountAsset() ?? '') : '';
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

            if ($groupBy === 'depreciation_group_tax') {
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
            case 'account':
                $category = $asset->getCategory();
                return $category ? ($category->getAccountAsset() ?? '') : '';
            case 'depreciation_group_tax':
                $groupTax = $asset->getDepreciationGroupTax();
                return $groupTax ? $groupTax->getFullName() : 'Bez zařazení';
            case 'entry_date':
                return $asset->getEntryDate()->format('Y');
            default:
                return 'all';
        }
    }

    protected function createDataForExport(?array $filter, array $assetGrouped): array
    {
        $columns = $filter['columns'];
        $columnsForSumming = $filter['summing'];
        $summedColumnsTemplate = [];
        foreach ($columns as $column) {
            if (in_array($column, $columnsForSumming)) {
                $summedColumnsTemplate[$column] = 0;
                continue;
            }
            $summedColumnsTemplate[$column] = '';
        }

        $result = [];

        foreach ($assetGrouped as $groupName => $records) {
            $summedColumns = $summedColumnsTemplate;
            /**
             * @var Asset $record
             */
            foreach ($records as $record) {
                $asset = [];

                foreach ($columns as $column) {
                    $value = $this->getColumnValueForExport($record, $column);
                    $asset[$column] = $value;
                    if (in_array($column, $columnsForSumming)) {
                        $summedColumns[$column] += $value;
                    }
                }
                $result[$groupName][$record->getId()] = $asset;
            }
            $result[$groupName]['summing'] = $summedColumns;
        }

        return $result;
    }

    protected function getColumnValueForExport(Asset $asset, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
            case 'type':
                return $asset->getAssetType()->getName();
            case 'inventory_number':
                return $asset->getInventoryNumber();
            case 'name':
                return $asset->getName();
            case 'category':
                return $asset->getCategory() ? $asset->getCategory()->getName() : 'Bez zařazení';
            case 'entry_date':
                return $asset->getEntryDate()->format('j. n. Y');
            case 'entry_price':
                return $asset->getEntryPrice();
            case 'increased_price':
                return $asset->getIncreasedEntryPrice();
            case 'account':
                $category = $asset->getCategory();
                return $category ? ($category->getAccountAsset() ?? '') : '';
            case 'depreciated_amount_tax':
                return $asset->getDepreciatedAmountTax();
            case 'residual_price_tax':
                return $asset->getAmortisedPriceTax();
            case 'depreciated_amount_accounting':
                return $asset->getDepreciatedAmountAccounting();
            case 'residual_price_accounting':
                return $asset->getAmortisedPriceAccounting();
            case 'depreciation_group_tax':
                $groupTax = $asset->getDepreciationGroupTax();
                return $groupTax ? $groupTax->getFullName() : 'Bez zařazení';
            case 'is_disposed':
                return $asset->isDisposed() ? 'ANO' : 'NE';
            default:
                return '';
        }
    }

    public function getColumnNamesFromFilter(?array $filter): array
    {
        $names = AssetColumns::NAMES_SHORT;

        $columns = $filter['columns'];
        $columnNames = [];

        foreach ($columns as $column) {
            $columnNames[$column] = $names[$column];
        }

        return $columnNames;
    }

    public function getFirstRowColumns(?array $filter): array
    {
        $columns = $filter['columns'];

        $taxColumns =
            [
                'depreciation_group_tax',
                'depreciated_amount_tax',
                'residual_price_tax'
            ];
        $accountingColumns =
            [
                'depreciated_amount_accounting',
                'residual_price_accounting'
            ];

        $before = 0;
        $after = 0;
        $taxColumnsCount = 0;
        $accountingColumnsCount = 0;
        $isBefore = true;

        foreach ($columns as $column) {
            if (in_array($column, $taxColumns)) {
                $isBefore = false;
                $taxColumnsCount++;
                continue;
            }
            if (in_array($column, $accountingColumns)) {
                $isBefore = false;
                $accountingColumnsCount++;
                continue;
            }
            if ($isBefore) {
                $before++;
                continue;
            }
            $after++;
        }

        $firstRow = [];
        $firstRow['before'] = $before;
        $firstRow['after'] = $after;
        $firstRow['tax'] = $taxColumnsCount;
        $firstRow['accounting'] = $accountingColumnsCount;

        return $firstRow;
    }
}
