<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Entity\AccountingEntity;
use App\Reports\Enums\DepreciationColumns;

class DepreciationHTMLGenerator
{
    private DepreciationReportsFilter $depreciationReportsFilter;

    public function __construct(
        DepreciationReportsFilter $depreciationReportsFilter,
    )
    {
        $this->depreciationReportsFilter = $depreciationReportsFilter;
    }

    public function generate(AccountingEntity $accountingEntity, string $filterParam): string
    {
        $filterDataStdClass = json_decode(urldecode($filterParam));
        $filter = json_decode(json_encode($filterDataStdClass), true);
        $depreciationsGrouped = $this->depreciationReportsFilter->getResults($accountingEntity, $filter);
        $groupedBy = $filter['grouping'] !== 'none' ? DepreciationColumns::NAMES[$filter['grouping']] : null;
        $summedColumns = $filter['summing'];
        $columns = $this->depreciationReportsFilter->getColumnNamesFromFilter($filter);

        $data = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" charset="UTF-8"/><title></title>';

        $css = '<style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px;}
                .table > * > * > * { padding: .2rem .2rem; border-bottom-width: 1px; border-color: rgb(222, 226, 230);} 
                .table > thead { vertical-align: bottom; }
                tbody, td, tfoot, th, thead, tr { border-width: 0; border-style: solid; border-color: inherit;}
                .table-bordered > * > * > * { border-width: 0 1px }
                table { border-collapse: collapse; border: 1px solid black;}
                '
        ;

        $css .= '</style>';
        $data .= $css;

        $data .= '</head><body>';

        $data .= '<h2>' . $accountingEntity->getName() . ' - Sestava odpisů </h2>';


        if($groupedBy) {
            $data .= '<h3 style="color: #0d6efd;">Seskupení: ' . $groupedBy . '</h3>';
        }
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

}
