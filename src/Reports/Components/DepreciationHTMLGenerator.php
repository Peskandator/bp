<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Entity\AccountingEntity;
use App\Majetek\Latte\Filters\FloatFilter;
use App\Majetek\ORM\AssetTypeRepository;
use App\Majetek\ORM\CategoryRepository;
use App\Majetek\ORM\DepreciationGroupRepository;
use App\Reports\Enums\DepreciationColumns;
use App\Utils\DateTimeFormatter;

class DepreciationHTMLGenerator
{
    private DepreciationReportsFilter $depreciationReportsFilter;
    private DateTimeFormatter $dateTimeFormatter;
    private FloatFilter $floatFilter;
    private CategoryRepository $categoryRepository;
    private AssetTypeRepository $assetTypeRepository;
    private DepreciationGroupRepository $depreciationGroupRepository;
    private HtmlToPdfGenerator $htmlToPdfGenerator;

    public function __construct(
        DepreciationReportsFilter $depreciationReportsFilter,
        DateTimeFormatter $dateTimeFormatter,
        FloatFilter $floatFilter,
        CategoryRepository $categoryRepository,
        AssetTypeRepository $assetTypeRepository,
        DepreciationGroupRepository $depreciationGroupRepository,
        HtmlToPdfGenerator $htmlToPdfGenerator,
    )
    {
        $this->depreciationReportsFilter = $depreciationReportsFilter;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->floatFilter = $floatFilter;
        $this->categoryRepository = $categoryRepository;
        $this->assetTypeRepository = $assetTypeRepository;
        $this->depreciationGroupRepository = $depreciationGroupRepository;
        $this->htmlToPdfGenerator = $htmlToPdfGenerator;
    }

    public function generate(AccountingEntity $accountingEntity, string $filterParam): string
    {
        $filterDataStdClass = json_decode(urldecode($filterParam));
        $filter = json_decode(json_encode($filterDataStdClass), true);
        $depreciationsGrouped = $this->depreciationReportsFilter->getResults($accountingEntity, $filter);
        $groupedBy = $filter['grouping'] !== 'none' ? DepreciationColumns::NAMES[$filter['grouping']] : null;
        $sorting = DepreciationColumns::NAMES[$filter['sorting']] ?? null;
        $summedColumns = $filter['summing'];
        $columns = $this->depreciationReportsFilter->getColumnNamesFromFilter($filter);

        $data = $this->htmlToPdfGenerator->generateHtmlHead();

        $data .= '<h2>' . $accountingEntity->getName() . ' - Sestava odpisů </h2>';

        if($groupedBy) {
            $data .= '<h3 style="color: #0d6efd;">Seskupení: ' . $groupedBy . '</h3>';
        }
        if($sorting) {
            $data .= '<h3 style="color: #0d6efd;">Třídění: ' . $sorting . '</h3>';
        }

        $filterData = $this->getFilterDescription($filter);

        $data .= $filterData;

        foreach ($depreciationsGrouped as $groupName => $group) {
            if ($groupName !== 'all') {
                $data .= '<h3>' . $groupName . '</h3>';
            }
            $data .= '<table style="margin-top: 16px" class="table table-bordered">';
            $data .= '<thead>';

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
            foreach ($group as $depreciationId => $assetData) {
                $columnCounter = 0;
                if ($depreciationId === 'summing' && count($summedColumns) === 0) {
                    continue;
                }
                $data .= '<tr>';
                foreach ($assetData as $columnName => $val) {
                    $columnCounter++;

                    if ($depreciationId === 'summing') {
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
        $depreciationGroups = $filter['depreciation_groups'] ?? null;
        $fromAccountDebited = $filter['account_from_debited'] ?? null;
        $toAccountDebited = $filter['account_to_debited'] ?? null;
        $fromAccountCredited = $filter['account_from_credited'] ?? null;
        $toAccountCredited = $filter['account_to_credited'] ?? null;
        $onlyAccountable = $filter['only_accountable'] ?? false;


        if ($onlyAccountable) {
            $content .= 'Jen k zaúčtování : ANO';
            $content = $this->addNewLine($content);
        }

        $content .= $this->htmlToPdfGenerator->writeAssetTypeNames($allowedTypes, 'Typ: ');
        $content .= $this->writeCategoryNames($allowedCategories, 'Kategorie: ');
        $content .= $this->writeCategoryNames($allowedCategories, 'Kategorie: ');

        $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
        $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
        $fromDateStr = $fromDate ? $fromDate->format('j. n. Y') : '___';
        $toDateStr = $toDate ? $toDate->format('j. n. Y') : '___';
        if ($fromDate || $toDate) {
            $content .= 'Datum provedení:';
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
            $content .= 'Vstupní cena';
            if ($fromPrice !== null) {
                $content .= ' od ' . $this->floatFilter->__invoke($fromPrice);
            }
            if ($toPrice !== null) {
                $content .= ' do ' . $this->floatFilter->__invoke($toPrice);
            }
            $content = $this->addNewLine($content);
        }

        $fromIncreasedPrice = $filter['increased_price_from'] ?? null;
        $toIncreasedPrice = $filter['increased_price_to'] ?? null;
        if ($fromIncreasedPrice !== null || $toIncreasedPrice !== null) {
            $content .= 'Zvýšená vstupní cena';
            if ($fromIncreasedPrice !== null) {
                $content .= ' od ' . $this->floatFilter->__invoke($fromIncreasedPrice);
            }
            if ($toIncreasedPrice !== null) {
                $content .= ' do ' . $this->floatFilter->__invoke($toIncreasedPrice);
            }
            $content = $this->addNewLine($content);
        }

        $depreciationAmountFrom = $filter['depreciation_amount_from'] ?? null;
        $depreciationAmountTo = $filter['depreciation_amount_to'] ?? null;
        if ($depreciationAmountFrom !== null || $depreciationAmountTo !== null) {
            $content .= 'Výše odpisu';
            if ($depreciationAmountFrom !== null) {
                $content .= ' od ' . $this->floatFilter->__invoke($depreciationAmountFrom);
            }
            if ($depreciationAmountTo !== null) {
                $content .= ' do ' . $this->floatFilter->__invoke($depreciationAmountTo);
            }
            $content = $this->addNewLine($content);
        }

        if ($fromAccountDebited || $toAccountDebited) {
            $content .= 'Účet MD:';
            if ($fromAccountDebited !== null) {
                $content .= ' od ' . $fromAccountDebited;
            }
            if ($toAccountDebited !== null) {
                $content .= ' do ' . $toAccountDebited;
            }
            $content = $this->addNewLine($content);
        }

        if ($fromAccountCredited || $toAccountCredited) {
            $content .= 'Účet DAL:';
            if ($fromAccountCredited !== null) {
                $content .= ' od ' . $fromAccountCredited;
            }
            if ($toAccountCredited !== null) {
                $content .= ' do ' . $toAccountCredited;
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
