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
        $movements = $this->currentEntity->getDepreciationTaxMovements();
        $result = [];

        $allowedCategories = $filter['categories'] ?? null;
        $depreciationGroups = $filter['depreciation_groups'] ?? null;
        $fromPrice = $filter['entry_price_from'] ?? null;
        $toPrice = $filter['entry_price_to'] ?? null;
        $fromIncreasedPrice = $filter['increased_price_from'] ?? null;
        $toIncreasedPrice = $filter['increased_price_to'] ?? null;
        $fromAccountDebited = $filter['account_from_debited'] ?? null;
        $toAccountDebited = $filter['account_to_debited'] ?? null;
        $fromAccountCredited = $filter['account_from_credited'] ?? null;
        $toAccountCredited = $filter['account_to_credited'] ?? null;
        $onlyAccountable = $filter['only_accountable'] ?? false;

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            $asset = $movement->getAsset();
            $depreciation = $movement->getDepreciation();

            if ($this->currentEntity->getId() !== $movement->getEntity()->getId()) {
                continue;
            }

            if (!$movement->isAccountable() && $onlyAccountable) {
                continue;
            }
            if ($allowedCategories && count($allowedCategories) > 0 && !in_array($asset->getCategory()->getId(), $allowedCategories)) {
                continue;
            }
            if ($depreciationGroups && count($depreciationGroups) > 0 && !in_array($depreciation->getDepreciationGroup()->getId(), $depreciationGroups)) {
                continue;
            }

            $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($fromDate && $movement->getDate() < $fromDate) {
                continue;
            }
            $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($toDate && $movement->getDate() > $toDate) {
                continue;
            }

            $entryPrice = $depreciation?->getEntryPrice();
            if ($fromPrice !== null && $entryPrice !== null && $entryPrice < $fromPrice) {
                continue;
            }
            if ($toPrice !== null && $entryPrice !== null && $entryPrice > $toPrice) {
                continue;
            }

            $increasedPrice = $depreciation?->getIncreasedEntryPrice();
            if ($fromIncreasedPrice !== null && $increasedPrice !== null && $increasedPrice < $fromIncreasedPrice) {
                continue;
            }
            if ($toIncreasedPrice !== null && $increasedPrice !== null && $increasedPrice > $toIncreasedPrice) {
                continue;
            }

            $accountDebited = $movement->getAccountDebited();
            if ($fromAccountDebited && $accountDebited < $fromAccountDebited) {
                continue;
            }
            if ($toAccountDebited && $accountDebited > $toAccountDebited) {
                continue;
            }

            $accountCredited = $movement->getAccountCredited();
            if ($fromAccountCredited && $accountCredited < $fromAccountCredited) {
                continue;
            }
            if ($toAccountCredited && $accountCredited > $toAccountCredited) {
                continue;
            }

            $result[] = $movement;
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

    protected function getSortByColumnValue(Movement $movement, $column): float|int|null|string|\DateTimeInterface
    {
        $asset = $movement->getAsset();
        $depreciation = $movement->getDepreciation();

        return match ($column) {
            'asset_name' => $asset->getName(),
            'depreciation_group_tax' => $depreciation->getDepreciationGroup()->getFullName(),
            'year' => $depreciation->getYear(),
            'execution_date' => $movement->getDate(),
            'account_debited' => $movement->getAccountDebited(),
            'account_credited' => $movement->getAccountCredited(),
            'entry_price' => $depreciation->getEntryPrice(),
            'increased_price' => $depreciation->getIncreasedEntryPrice(),
            'depreciation_amount' => $depreciation->getDepreciationAmount(),
            'depreciated_amount' => $depreciation->getDepreciatedAmount(),
            'residual_price' => $depreciation->getResidualPrice(),
            default => '',
        };
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

    protected function getGroupByColumnValue(Movement $movement, $column): float|int|null|string|\DateTimeInterface
    {
        $asset = $movement->getAsset();
        $depreciation = $movement->getDepreciation();

        switch ($column) {
            case 'asset_name':
                return $asset->getName();
            case 'category':
                $category = $asset->getCategory();
                return $category ? $category->getName() : 'Bez zařazení';
            case 'depreciation_group_tax':
                return $depreciation->getDepreciationGroup()->getFullName();
            case 'year':
                return $depreciation->getYear() ?? 'Bez zařazení';
            case 'account_debited':
                return $movement->getAccountDebited();
            case 'account_credited':
                return $movement->getAccountCredited();
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

    protected function getColumnValueForExport(Movement $movement, $column): float|int|null|string|\DateTimeInterface
    {
        $asset = $movement->getAsset();
        $depreciation = $movement->getDepreciation();

        switch ($column) {
            case 'asset_name':
                return $asset->getName();
            case 'inventory_number':
                return $asset->getInventoryNumber();
            case 'depreciation_group_tax':
                return $depreciation->getDepreciationGroup()->getFullName();
            case 'category':
                $category = $asset->getCategory();
                return $category ? $category->getName() : '';
            case 'year':
                return $depreciation->getYear();
            case 'depreciation_year':
                return $depreciation->getDepreciationYear();
            case 'execution_date':
                return $movement->getDate()->format('j.n.Y');
            case 'rate':
                return $depreciation->getRate();
            case 'percentage':
                return $depreciation->getPercentage();
            case 'account_debited':
                return $movement->getAccountDebited();
            case 'account_credited':
                return $movement->getAccountCredited();
            case 'entry_price':
                return $depreciation->getEntryPrice();
            case 'increased_price':
                return $depreciation->getIncreasedEntryPrice();
            case 'residual_price':
                return $depreciation->getResidualPrice();
            case 'depreciation_amount':
                return $depreciation->getDepreciationAmount();
            case 'depreciated_amount':
                return $depreciation->getDepreciatedAmount();
            case 'is_accountable':
                return $movement->isAccountable() ? 'ANO' : 'NE';
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
