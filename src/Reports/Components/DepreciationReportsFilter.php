<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Entity\AccountingEntity;
use App\Entity\Movement;
use App\Reports\Enums\DepreciationColumns;
use App\Utils\DateTimeFormatter;
use App\Utils\EnumerableSorter;

class DepreciationReportsFilter
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

        $filteredDepreciations = $this->getFilteredResults($filter);
        $sortedDepreciations = $this->sortDepreciations($filter, $filteredDepreciations);
        $groupedDepreciations = $this->groupDepreciations($filter, $sortedDepreciations);
        $data = $this->createDataForExport($filter, $groupedDepreciations);

        return $data;
    }

    protected function getFilteredResults(?array $filter): array
    {
        $depreciationMovements = $this->currentEntity->getDepreciationTaxMovements();




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
         * @var Movement $depreciationMovement
         */
        foreach ($depreciationMovements as $depreciationMovement) {
            if ($this->currentEntity->getId() !== $depreciationMovement->getEntity()->getId()) {
                continue;
            }

//            if ($asset->isDisposed() && !$withDisposed) {
//                continue;
//            }
//
//            if ($allowedTypes && count($allowedTypes) > 0 && !in_array($asset->getAssetType()->getId(), $allowedTypes)) {
//                continue;
//            }
//
//            if ($allowedCategories && count($allowedCategories) > 0 && !in_array($asset->getCategory()->getId(), $allowedCategories)) {
//                continue;
//            }
//
//            if ($allowedPlaces && count($allowedPlaces) > 0 && !in_array($asset->getPlace()->getId(), $allowedPlaces)) {
//                continue;
//            }
//
//            $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
//            if ($fromDate && $asset->getEntryDate() < $fromDate) {
//                continue;
//            }
//            $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
//            if ($toDate && $asset->getEntryDate() > $toDate) {
//                continue;
//            }
//
//            $currentYear = (int)date("Y");
//            if ($fromPrice !== null && $asset->getPriceForYear($currentYear) < $fromPrice) {
//                continue;
//            }
//            if ($toPrice !== null && $asset->getPriceForYear($currentYear) > $toPrice) {
//                continue;
//            }
//
//            $category = $asset->getCategory();
//            $assetAccount = $category?->getAccountAsset();
//            if ($fromAccount && $assetAccount < $fromAccount) {
//                continue;
//            }
//            if ($toAccount && $assetAccount > $toAccount) {
//                continue;
//            }

            $result[] = $depreciationMovement;
        }

        return $result;
    }

    protected function sortDepreciations(?array $filter, array $records): array
    {
        $sortBy = $filter['sorting'];
        $depreciations = $records;

        if ($sortBy !== null) {
            usort($depreciations, function (Movement $first, Movement $second) use ($sortBy) {
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

        return $depreciations;
    }

    protected function getSortByColumnValue(Movement $depreciation, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
//            case 'entry_date':
//                return $depreciation->getEntryDate();
//            case 'name':
//                return $depreciation->getName();
//            case 'entry_price':
//                return $depreciation->getEntryPrice();
//            case 'increased_price':
//                return $depreciation->getIncreasedEntryPrice();
//            case 'account':
//                $category = $depreciation->getCategory();
//                return $category ? ($category->getAccountAsset() ?? '') : '';
//            case 'depreciated_amount_tax':
//                return $depreciation->getDepreciatedAmountTax();
//            case 'residual_price_tax':
//                return $depreciation->getAmortisedPriceTax();
//            case 'depreciated_amount_accounting':
//                return $depreciation->getDepreciatedAmountAccounting();
//            case 'residual_price_accounting':
//                return $depreciation->getAmortisedPriceAccounting();
            default:
                return '';
//                return $depreciation->getInventoryNumber();
        }
    }

    protected function groupDepreciations(?array $filter, array $records): array
    {
        $groupBy = $filter['grouping'];

        $depreciations = [];

        if ($groupBy !== null || $groupBy !== 'none') {
            foreach ($records as $record) {
                $groupByValue = $this->getGroupByColumnValue($record, $groupBy);
                $depreciations[$groupByValue][] = $record;
            }

            if ($groupBy === 'depreciation_group_tax') {
                $depreciations = $this->enumerableSorter->sortGroupsInArrayByMethodAndNumber($depreciations);
            }

            return $depreciations;
        }
        $depreciations['none'] = $records;
        return $depreciations;
    }

    protected function getGroupByColumnValue(Movement $depreciation, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
//            case 'type':
//                return $depreciation->getAssetType()->getName();
//            case 'category':
//                return $depreciation->getCategory() ? $depreciation->getCategory()->getName() : 'Bez zařazení';
//            case 'account':
//                $category = $depreciation->getCategory();
//                return $category ? ($category->getAccountAsset() ?? '') : '';
//            case 'depreciation_group_tax':
//                $groupTax = $depreciation->getDepreciationGroupTax();
//                return $groupTax ? $groupTax->getFullName() : 'Bez zařazení';
//            case 'entry_date':
//                return $depreciation->getEntryDate()->format('Y');
            default:
                return 'all';
        }
    }

    protected function createDataForExport(?array $filter, array $depreciationsGrouped): array
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

        foreach ($depreciationsGrouped as $groupName => $records) {
            $summedColumns = $summedColumnsTemplate;
            /**
             * @var Movement $record
             */
            foreach ($records as $record) {
                $depreciation = [];

                foreach ($columns as $column) {
                    $value = $this->getColumnValueForExport($record, $column);
                    $depreciation[$column] = $value;
                    if (in_array($column, $columnsForSumming)) {
                        $summedColumns[$column] += $value;
                    }
                }
                $result[$groupName][$record->getId()] = $depreciation;
            }
            $result[$groupName]['summing'] = $summedColumns;
        }

        return $result;
    }

    protected function getColumnValueForExport(Movement $depreciation, $column): float|int|null|string|\DateTimeInterface
    {
        switch ($column) {
//            case 'type':
//                return $depreciation->getAssetType()->getName();
//            case 'inventory_number':
//                return $depreciation->getInventoryNumber();
//            case 'name':
//                return $depreciation->getName();
//            case 'category':
//                return $depreciation->getCategory() ? $depreciation->getCategory()->getName() : 'Bez zařazení';
//            case 'entry_date':
//                return $depreciation->getEntryDate()->format('j. n. Y');
//            case 'entry_price':
//                return $depreciation->getEntryPrice();
//            case 'increased_price':
//                return $depreciation->getIncreasedEntryPrice();
//            case 'account':
//                $category = $depreciation->getCategory();
//                return $category ? ($category->getAccountAsset() ?? '') : '';
//            case 'depreciated_amount_tax':
//                return $depreciation->getDepreciatedAmountTax();
//            case 'residual_price_tax':
//                return $depreciation->getAmortisedPriceTax();
//            case 'depreciated_amount_accounting':
//                return $depreciation->getDepreciatedAmountAccounting();
//            case 'residual_price_accounting':
//                return $depreciation->getAmortisedPriceAccounting();
//            case 'depreciation_group_tax':
//                $groupTax = $depreciation->getDepreciationGroupTax();
//                return $groupTax ? $groupTax->getFullName() : 'Bez zařazení';
//            case 'is_disposed':
//                return $depreciation->isDisposed() ? 'ANO' : 'NE';
            default:
                return '';
        }
    }

    public function getColumnNamesFromFilter(?array $filter): array
    {
        $names = DepreciationColumns::NAMES_SHORT;

        $columns = $filter['columns'];
        $columnNames = [];

        foreach ($columns as $column) {
            $columnNames[$column] = $names[$column];
        }

        return $columnNames;
    }
}
