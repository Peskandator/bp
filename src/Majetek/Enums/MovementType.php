<?php


declare(strict_types=1);

namespace App\Majetek\Enums;

final class MovementType
{
    public const INCLUSION = 1;
    public const DISPOSAL = 2;
    public const DEPRECIATION_TAX = 3;
    public const DEPRECIATION_ACCOUNTING = 4;
    public const INCREASE_ENTRY_PRICE_TAX = 5;
    public const DECREASE_ENTRY_PRICE_TAX = 6;
    public const INCREASE_ENTRY_PRICE_ACCOUNTING= 7;
    public const DECREASE_ENTRY_PRICE_ACCOUNTING = 8;

    public const NAMES =
        [
            1 => 'Zařazení',
            2 => 'Vyřazení',
            3 => 'Daňový odpis',
            4 => 'Účetní odpis',
            5 => 'Zvýšení daň. VC',
            6 => 'Snížení daň. VC',
            7 => 'Zvýšení úč. VC',
            8 => 'Snížení úč. VC',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
