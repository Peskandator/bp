<?php


declare(strict_types=1);

namespace App\Majetek\Enums;

final class AssetColumns
{
    public const NAMES =
        [
            'type' => 'Typ',
            'inventory_number' => 'Inventární číslo',
            'name' => 'Název',
            'entry_date' => 'Datum zařazení',
            'category' => 'Kategorie',
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'depreciation_group_tax' => 'Daňová odpisová skupina',
            'depreciated_amount_tax' => 'Daňové oprávky',
            'residual_price_tax' => 'Daňová zůstatková cena',
            'depreciation_group_accounting' => 'Účetní odpisová skupina',
            'depreciated_amount_accounting' => 'Účetní oprávky',
            'residual_price_accounting' => 'Účetní zůstatková cena',
            'is_disposed' => 'Vyřazeno',
        ]
    ;

    public const SORTING_BY =
        [
            'inventory_number' => 'Inventární číslo',
            'name' => 'Název',
            'entry_date' => 'Datum zařazení',
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'depreciated_amount_tax' => 'Daňové oprávky',
            'residual_price_tax' => 'Daňová zůstatková cena',
            'depreciated_amount_accounting' => 'Účetní oprávky',
            'residual_price_accounting' => 'Účetní zůstatková cena',
        ]
    ;

    public const SUMMING_BY =
        [
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'depreciated_amount_tax' => 'Daňové oprávky',
            'residual_price_tax' => 'Daňová zůstatková cena',
            'depreciated_amount_accounting' => 'Účetní oprávky',
            'residual_price_accounting' => 'Účetní zůstatková cena',
        ]
    ;

    public const GROUPING_BY =
        [
            'none' => 'Žádné',
            'type' => 'Typ',
            'category' => 'Kategorie',
            'depreciation_group_accounting' => 'Účetní odpisová skupina',
            'depreciation_group_tax' => 'Daňová odpisová skupina',
            'entry_date' => 'Datum zařazení',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
