<?php


declare(strict_types=1);

namespace App\Reports\Enums;

final class AssetColumns
{
    public const NAMES =
        [
            'name' => 'Název',
            'type' => 'Typ',
            'inventory_number' => 'Inventární číslo',
            'entry_date' => 'Datum zařazení',
            'category' => 'Kategorie',
            'entry_price' => 'Vstupní cena',
            'increased_price' => 'Zvýšená vstupní cena',
            'account' => 'Účet',
            'depreciation_group_tax' => 'Daňová odpisová skupina',
            'depreciated_amount_tax' => 'Daňové oprávky',
            'residual_price_tax' => 'Daňová zůstatková cena',
            'depreciated_amount_accounting' => 'Účetní oprávky',
            'residual_price_accounting' => 'Účetní zůstatková cena',
            'is_disposed' => 'Vyřazeno',
        ]
    ;
    public const NAMES_SHORT =
        [
            'name' => 'Název',
            'type' => 'Typ',
            'inventory_number' => 'Inv.č.',
            'entry_date' => 'Datum zař.',
            'category' => 'Kat.',
            'entry_price' => 'VC',
            'increased_price' => 'Zvýš.VC',
            'account' => 'Účet',
            'depreciation_group_tax' => 'Odp.sk.',
            'depreciated_amount_tax' => 'Oprávky',
            'residual_price_tax' => 'ZC',
            'depreciated_amount_accounting' => 'Oprávky',
            'residual_price_accounting' => 'ZC',
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
            'account' => 'Účet',
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
            'account' => 'Účet',
            'category' => 'Kategorie',
            'depreciation_group_tax' => 'Daňová odpisová skupina',
            'entry_date' => 'Datum zařazení',
        ]
    ;

    // TODO: uděat main_content responzivní - odebrat margin po stranách
    // středisko
    // main-
    // přidat veškeré sloupce

    private function __construct()
    {
        // empty
    }
}
