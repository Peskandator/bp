<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Entity\AccountingEntity;
use App\Majetek\Latte\Filters\FloatFilter;
use App\Majetek\ORM\AssetTypeRepository;
use App\Majetek\ORM\CategoryRepository;
use App\Majetek\ORM\PlaceRepository;
use App\Reports\Enums\AssetColumns;
use App\Utils\DateTimeFormatter;

class AssetHTMLGenerator
{
    private AssetReportsFilter $assetReportsFilter;
    private DateTimeFormatter $dateTimeFormatter;
    private FloatFilter $floatFilter;
    private PlaceRepository $placeRepository;
    private CategoryRepository $categoryRepository;
    private AssetTypeRepository $assetTypeRepository;
    private HtmlToPdfGenerator $htmlToPdfGenerator;

    public function __construct(
        AssetReportsFilter $assetReportsFilter,
        DateTimeFormatter $dateTimeFormatter,
        FloatFilter $floatFilter,
        PlaceRepository $placeRepository,
        CategoryRepository $categoryRepository,
        AssetTypeRepository $assetTypeRepository,
        HtmlToPdfGenerator $htmlToPdfGenerator,
    )
    {
        $this->assetReportsFilter = $assetReportsFilter;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->floatFilter = $floatFilter;
        $this->placeRepository = $placeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->assetTypeRepository = $assetTypeRepository;
        $this->htmlToPdfGenerator = $htmlToPdfGenerator;
    }

    public function generate(AccountingEntity $accountingEntity, string $filterParam): string
    {
        $filterDataStdClass = json_decode(urldecode($filterParam));
        $filter = json_decode(json_encode($filterDataStdClass), true);
        $assetsGrouped = $this->assetReportsFilter->getResults($accountingEntity, $filter);
        $groupedBy = $filter['grouping'] !== 'none' ? AssetColumns::NAMES[$filter['grouping']] : null;
        $sorting = AssetColumns::NAMES[$filter['sorting']] ?? null;
        $columns = $this->assetReportsFilter->getColumnNamesFromFilter($filter);
        $firstRow = $this->assetReportsFilter->getFirstRowColumns($filter);
        $summedColumns = $filter['summing'];

        $data = $this->htmlToPdfGenerator->generateHtmlHead();

        $data .= '<h2>' . $accountingEntity->getName() . ' - Sestava majetku </h2>';

        if($groupedBy) {
            $data .= '<div style="color: #0d6efd; font-size: 14px;">Seskupení: ' . $groupedBy . '</div>';
        }
        if($sorting) {
            $data .= '<div style="color: #0d6efd; font-size: 14px;">Třídění: ' . $sorting . '</div>';
        }

        $filterData = $this->getFilterDescription($filter);

        $data .= $filterData;

        foreach ($assetsGrouped as $groupName => $group) {
            if ($groupName !== 'all') {
                $data .= '<h3>' . $groupName . '</h3>';
            }
            $data .= '<table style="margin-top: 16px" class="table table-bordered">';
            $data .= '<thead>';

            if ($firstRow['tax'] > 0 || $firstRow['accounting'] > 0) {
                $data .= '<tr>';

                for ($i = 0; $i < $firstRow['before']; $i++) {
                    if ($i === 0) {
                        $data .= '<th style="border-left-color: black"></th>';
                        continue;
                    }
                    $data .= '<th></th>';
                }
                if ($firstRow['tax'] > 0) {
                    $data .= '<th colspan="' . $firstRow['tax'] . '" style="font-size: 16px;">Daňové</th>';
                }
                if ($firstRow['accounting'] > 0) {
                    $data .= '<th colspan="' . $firstRow['accounting'] . '" style="font-size: 16px;">Účetní</th>';
                }
                for ($i = 0; $i < $firstRow['after']; $i++) {
                    if ($i === $firstRow['after'] - 1) {
                        $data .= '<th style="border-right-color: black"></th>';
                        continue;
                    }
                    $data .= '<th></th>';
                }
                $data .= '</tr>';
            }

            $data .= '<tr>';
                $counter = 0;
                foreach ($columns as $column) {
                    $counter++;
                    if ($counter === 1) {
                        $data .= '<th style="border-bottom: solid 1px black; border-left-color: black">' . $column . '</th>';
                        continue;
                    }
                    if ($counter === count($columns)) {
                        $data .= '<th style="border-bottom: solid 1px black; border-right-color: black">' . $column . '</th>';
                        continue;
                    }
                    $data .= '<th style="border-bottom: solid 1px black">' . $column . '</th>';
                }
            $data .= '</tr>';
            $data .= '</thead>';

            $data .= '<tbody>';
            foreach ($group as $assetId => $assetData) {
                $columnCounter = 0;
                if ($assetId === 'summing' && count($summedColumns) === 0) {
                    continue;
                }
                $data .= '<tr>';
                foreach ($assetData as $columnName => $val) {
                    $columnCounter++;

                    if ($assetId === 'summing') {
                        if ($columnCounter === 1) {
                            $data .= '<td style="border-top: solid 1px black; border-left-color: black">';
                        } else if ($columnCounter === count($assetData)) {
                            $data .= '<td style="border-top: solid 1px black; border-right-color: black">';
                        } else {
                            $data .= '<td style="border-top: solid 1px black">';
                        }
                        $data .= '<b>' . $val . '</b>';
                        $data .= '</td>';
                        continue;
                    }
                    if ($columnCounter === 1) {
                        $data .= '<td style="border-left-color: black">';
                    } else if ($columnCounter === count($assetData)) {
                        $data .= '<td style="border-right-color: black">';
                    } else {
                        $data .= '<td>';
                    }
                    $data .= $val;
                    $data .= '</td>';
                }
                $data .= '</tr>';
            }

            $data .= '</tbody>';
            $data .= '</table>';
        }
        $data .= '</body></html>';

        return $data;
    }

    protected function getFilterDescription(?array $filter): string
    {
        $content = '';

        $allowedTypes = $filter['types'] ?? null;
        $allowedCategories = $filter['categories'] ?? null;
        $allowedPlaces = $filter['places'] ?? null;

        $withDisposed = $filter['disposed'] ?? null;
        $fromAccount = $filter['account_from'] ?? null;
        $toAccount = $filter['account_to'] ?? null;

        if ($withDisposed) {
            $content .= 'Vyřazené: ANO';
            $content = $this->addNewLine($content);
        }

        $content .= $this->htmlToPdfGenerator->writeAssetTypeNames($allowedTypes, 'Typ: ');
        $content .= $this->htmlToPdfGenerator->writeCategoryNames($allowedCategories, 'Kategorie: ');
        $content .= $this->htmlToPdfGenerator->writePlaceNames($allowedPlaces, 'Místa: ');

        $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
        $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
        $fromDateStr = $fromDate ? $fromDate->format('j. n. Y') : '___';
        $toDateStr = $toDate ? $toDate->format('j. n. Y') : '___';
        if ($fromDate || $toDate) {
            $content .= 'Datum zařazení:';
            if ($fromDate) {
                $content .= ' od ' . $fromDateStr;
            }
            if ($fromDate) {
                $content .= ' do ' . $toDateStr;
            }
            $content = $this->addNewLine($content);
        }

        $fromPrice = $filter['entry_price_from'] ?? null;
        $toPrice = $filter['entry_price_to'] ?? null;

        if ($fromPrice !== null || $toPrice !== null) {
            $content .= 'Vstupní cena:';
            if ($fromPrice !== null) {
                $content .= ' od ' . $this->floatFilter->__invoke($fromPrice);
            }
            if ($toPrice !== null) {
                $content .= ' do ' . $this->floatFilter->__invoke($toPrice);
            }
            $content = $this->addNewLine($content);
        }
        if ($fromAccount || $toAccount) {
            $content .= 'Účet:';
            if ($fromAccount !== null) {
                $content .= ' od ' . $fromAccount;
            }
            if ($toAccount !== null) {
                $content .= ' do ' . $toAccount;
            }
            $content = $this->addNewLine($content);
        }

        if ($content !== '')
        {
            return '
                <div style="color: #0d6efd; font-size: 14px;">Filtr:</div>
                    <div style="color: #0d6efd; font-size: 12px; margin-left: 20px">'
                        . $content .
                    '</div>
                </div>'
            ;
        }
        return '';
    }

    protected function addNewLine(string $html): string
    {
        return $html . '<br>';
    }
}
