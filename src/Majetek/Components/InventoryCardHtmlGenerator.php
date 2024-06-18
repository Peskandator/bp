<?php
declare(strict_types=1);

namespace App\Majetek\Components;

use App\Entity\Asset;
use App\Entity\Movement;
use App\Majetek\Latte\Filters\FloatFilter;
use App\Utils\PriceFilter;

class InventoryCardHtmlGenerator
{
    private PriceFilter $priceFilter;
    private FloatFilter $floatFilter;

    public function __construct(
        PriceFilter $priceFilter,
        FloatFilter $floatFilter,
    )
    {
        $this->priceFilter = $priceFilter;
        $this->floatFilter = $floatFilter;
    }

    public function getHtmlData(Asset $asset): string
    {
        $entity = $asset->getEntity();
        $data = $this->generateHtmlHead();
        $increaseDate = $asset->getIncreaseDate() ? $asset->getIncreaseDate()->format('j. n. Y') : '';
        $disposalDate = $asset->getDisposalDate() ? $asset->getDisposalDate()->format('j. n. Y') : '';
        $disposalDateLabel = $disposalDate !== '' ? 'Datum vyřazení' : '';
        $location = $asset->getLocation() ? $asset->getLocation()->getName() : '';
        $place = $asset->getPlace() ? $asset->getPlace()->getName() : '';
        $acquisition = $asset->getAcquisition() ? $asset->getAcquisition()->getName() : '';
        $disposal = $asset->getDisposal() ? $asset->getDisposal()->getName() : '';
        $disposalLabel = $asset->getDisposal() ? 'Zp. vyřazení: '  : '';
        $groupTax = $asset->getDepreciationGroupTax();
        $depreciationGroupTax = $groupTax ? $groupTax->getFullName() : '';
        $groupTaxYearsMonthsLabel = 'Počet let: ';
        $groupTaxYearsMonths = $groupTax ? $groupTax->getYears() : '';
        if ($groupTax && $groupTax->getMonths()) {
            $groupTaxYearsMonthsLabel = 'Počet měsíců: ';
            $groupTaxYearsMonths = $groupTax->getMonths();
        }
        $hasTaxDepreciationsValue = $asset->hasTaxDepreciations() ? 'ANO' : 'NE';
        $onlyTaxDepreciations = $asset->isOnlyTax() ? 'ANO' : 'NE';

        $data .=
            '<h3 style="text-align: center"> Inventární karta majetku</h3>
            <table>
            <tr style="border-bottom: black 1px solid" class="header-row">
                <td><span class="first-cell">Účetní jednotka:</span></td>
                <td class="text-bold">'.$entity->getName() .'</td>
                <td></td>
                <td>IČO: </td>
                <td>'.$entity->getCompanyId().'</td>
            </tr>
            <tr style="border-bottom: black 1px solid" class="header-row">
                <td><span class="first-cell">Název: </span></td>
                <td class="text-bold">'.$asset->getName().'</td>
                <td></td>                
                <td><span class="first-cell">Inventární číslo: </span></td>
                <td class="text-bold">'.$asset->getInventoryNumber().'</td>
            </tr>
            <tr>
                <td>Datum zařazení: </td>
                <td class="text-bold">' . $asset->getEntryDate()->format('j. n. Y') . '</td>
                <td></td>
                <td>'.$disposalDateLabel.'</td>
                <td>' . $disposalDate. '</td>
            </tr>
            <tr>
                <td>Vstupní cena: </td>
                <td >' . $this->priceFilter->__invoke($asset->getEntryPrice()) . '</td>
                <td></td>
                <td>Počet kusů: </td>
                <td>'.$asset->getUnits().'</td>
            </tr>
            <tr>
                <td>Zvýšená VC: </td>
                <td >' . $this->priceFilter->__invoke($asset->getIncreasedEntryPriceForView()) . '</td>
                <td></td>
                <td>Datum zvýšení: </td>
                <td>'.$increaseDate.'</td>
            </tr>
            <tr>
                <td>Typ:</td>
                <td >' . $asset->getAssetType()->getName() . '</td>
                <td></td>
                <td>Kategorie: </td>
                <td>'.$asset->getCategory()->getName().'</td>
            </tr>
            <tr>
                <td>Středisko:</td>
                <td >' . $location . '</td>
                <td></td>
                <td>Místo: </td>
                <td>'.$place.'</td>
            </tr>
            <tr>
                <td>Výrobce:</td>
                <td >' . $asset->getProducer() . '</td>
                <td></td>
                <td>Doklad: </td>
                <td>'.$asset->getInvoiceNumber().'</td>
            </tr>
            <tr>
                <td>Způsob pořízení:</td>
                <td >'.$acquisition.'</td>
                <td></td>
                <td>VS: </td>
                <td>'.$asset->getVariableSymbol().'</td>
            </tr>
            <tr>
                <td>'.$disposalLabel.'</td>
                <td>'.$disposal.'</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr style="border-top: black 1px solid">
                <td>Odp. sk. a zp.</td>
                <td>'.$depreciationGroupTax.'</td>
                <td></td>
                <td>'.$groupTaxYearsMonthsLabel.'</td>
                <td>'.$groupTaxYearsMonths.'</td>
            </tr>
            <tr class="header-row">
                <td>Daňové odpisy</td>
                <td>'.$hasTaxDepreciationsValue.'</td>
                <td></td>
                <td>daňové = účetní</td>
                <td>'.$onlyTaxDepreciations.'</td>
            </tr>
            <tr>
                <td colspan="5"><div style="font-size: 16px; font-weight: bold">Pohyby k zaúčtování: </div></td>
            </tr>'
        ;

        $movements = $asset->getMovements();

        $data .=
            '<tr style="font-weight: bold">
                <td>Typ</td>
                <td>Datum</td>
                <td>Částka</td>
                <td>ZC</td>
                <td>Popis</td>
            </tr>'
        ;

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            if (!$movement->isAccountable()) {
                continue;
            }
            $movementRow =
                '<tr>
                    <td>'.$movement->getTypeName().'</td>
                    <td>'.$movement->getDate()->format('j. n. Y').'</td>
                    <td>'.$this->floatFilter->__invoke($movement->getValue()).'</td>
                    <td>'.$this->floatFilter->__invoke($movement->getResidualPrice()).'</td>
                    <td>'.$movement->getDescription().'</td>
                </tr>'
            ;

            $data .= $movementRow;
        }
        $data .=
            '<tr>
                <td colspan="5"><div style="font-size: 16px; font-weight: bold">Pohyby neurčené k zaúčtování: </div></td>
            </tr>
            <tr style="font-weight: bold">
                <td>Typ</td>
                <td>Datum</td>
                <td>Částka</td>
                <td>ZC</td>
                <td>Popis</td>
            </tr>'
        ;

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            if ($movement->isAccountable()) {
                continue;
            }
            $movementRow =
                '<tr>
                    <td>'.$movement->getTypeName().'</td>
                    <td>'.$movement->getDate()->format('j. n. Y').'</td>
                    <td>'.$this->floatFilter->__invoke($movement->getValue()).'</td>
                    <td>'.$this->floatFilter->__invoke($movement->getResidualPrice()).'</td>
                    <td>'.$movement->getDescription().'</td>
                </tr>'
            ;

            $data .= $movementRow;
        }

        $data .= '</table>';

        return $data;
    }

    public function generateHtmlHead(): string
    {
        $data = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" charset="UTF-8"/><title></title>';
        $css = '<style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 16px;}
                .table > thead { vertical-align: bottom; }
                tbody, td, tfoot, th, thead, tr { border-width: 0; border-style: solid; border-color: inherit;}
                .table-bordered > * > * > * { border-width: 0 1px }
                table { border-collapse: collapse; border: 2px solid black; width: 100%;}
                .header-row td {width: 27px}
                table > * > * > * { padding: .3rem .2rem;}
                .text-bold {font-weight: bold}
                ';
//        border-bottom-width: 1px; border-color: rgb(222, 226, 230)
        $css .= '</style>';
        $data .= $css;
        $data .= '</head><body>';
        return $data;
    }

    protected function addNewLine(string $html): string
    {
        return $html . '<br>';
    }
}
