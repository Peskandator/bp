<?php


declare(strict_types=1);

namespace App\Reports\Enums;

final class DepreciationColumns
{
    public const NAMES =
        [
            'asset_name' => 'Název majetku',
            'inventory_number' => 'Inventární číslo',
            'category' => 'Kategorie majetku',
            'depreciation_group_tax' => 'Odpisová skupina, způsob',
            'year' => 'Rok',
            'execution_date' => 'Datum',
            'depreciation_year' => 'Rok odpisu',
            'account_debited' => 'Účet MD',
            'account_credited' => 'Účet DAL',
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'rate' => 'Sazba',
            'percentage' => 'Procento',
            'depreciation_amount' => 'Výše odpisu',
            'depreciated_amount' => 'Oprávky',
            'residual_price' => 'Zůstatková cena',
            'is_accountable' => 'Zaúčtovat',
        ]
    ;
    public const NAMES_SHORT =
        [
            'asset_name' => 'Maj.',
            'inventory_number' => 'Inv.č.',
            'category' => 'Kat.',
            'depreciation_group_tax' => 'Odp.sk.,zp.',
            'year' => 'Rok',
            'execution_date' => 'Datum',
            'depreciation_year' => 'Rok odp.',
            'account_debited' => 'Účet MD',
            'account_credited' => 'Účet DAL',
            'entry_price' => 'VC',
            'increased_price' => 'Zvýš. VC',
            'rate' => 'Sazba',
            'percentage' => '%',
            'depreciation_amount' => 'Odpis',
            'depreciated_amount' => 'Opr.',
            'residual_price' => 'ZC',
            'is_accountable' => 'Zaúčtovat',
        ]
    ;

    public const SORTING_BY =
        [
            'asset_name' => 'Název majetku',
            'depreciation_group_tax' => 'Odpisová skupina, způsob',
            'year' => 'Rok',
            'execution_date' => 'Datum',
            'account_debited' => 'Účet MD',
            'account_credited' => 'Účet DAL',
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'depreciation_amount' => 'Výše odpisu',
            'depreciated_amount' => 'Oprávky',
            'residual_price' => 'Zůstatková cena',
        ]
    ;

    public const SUMMING_BY =
        [
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'depreciation_amount' => 'Výše odpisu',
            'depreciated_amount' => 'Oprávky',
            'residual_price' => 'Zůstatková cena',
        ]
    ;

    public const GROUPING_BY =
        [
            'none' => 'Žádné',
            'asset_name' => 'Název majetku',
            'category' => 'Kategorie',
            'depreciation_group_tax' => 'Odpisová skupina, způsob',
            'year' => 'Rok',
            'account_debited' => 'Účet MD',
            'account_credited' => 'Účet DAL',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
