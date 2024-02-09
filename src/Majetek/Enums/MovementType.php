<?php


declare(strict_types=1);

namespace App\Majetek\Enums;

final class MovementType
{
    public const INCLUSION = 1;
    public const DISPOSAL = 2;
    public const DEPRECIATION_TAX = 3;
    public const DEPRECIATION_ACCOUNTING = 4;
    public const ENTRY_PRICE_CHANGE = 5;
    public const LOCATION_CHANGE = 6;
    public const PLACE_CHANGE = 7;

    public const NAMES =
        [
            1 => 'Zařazení',
            2 => 'Vyřazení',
            3 => 'Daňový odpis',
            4 => 'Účetní odpis',
            5 => 'Změna VC',
            6 => 'Změna střediska',
            7 => 'Změna místa',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
