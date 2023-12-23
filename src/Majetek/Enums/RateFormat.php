<?php


declare(strict_types=1);

namespace App\Majetek\Enums;

final class RateFormat
{
    public const PERCENTAGE = 1;
    public const COEFFICIENT = 2;
    public const OWN_METHOD = 3;
    public const NAMES =
        [
            1 => 'Procento',
            2 => 'Koeficient',
            3 => 'Vlastní způsob',
        ]
    ;

    public const NAMES_SHORT =
        [
            1 => 'P',
            2 => 'K',
            3 => 'VZ',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
