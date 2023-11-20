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
            0 => 'Procento',
            1 => 'Koeficient',
            2 => 'Vlastní způsob',
        ]
    ;

    private function __construct()
    {
        // empty
    }
}
