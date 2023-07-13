<?php

declare(strict_types=1);

namespace App\Majetek\Enums;

final class AcquisitionsDefault
{
    public const PURCHASE = 1;
    public const MANUFACTURE = 2;
    public const CONTRIBUTION = 3;
    public const GIFT = 4;
    public const DONATION = 5;
    public const OTHER = 6;


    public static function getDefaultAcquisitions(): array
    {
        return [
            self::PURCHASE,
            self::MANUFACTURE,
            self::CONTRIBUTION,
            self::GIFT,
            self::DONATION,
            self::OTHER,
        ];
    }

    private function __construct()
    {
        // empty
    }
}
