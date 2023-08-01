<?php

declare(strict_types=1);

namespace App\Majetek\Enums;

final class DepreciationMethod
{
    public const UNIFORM = 1;
    public const ACCELERATED = 2;
    public const EXTRAORDINARY= 3;
    public const NAMES =
        [
            1 => 'Rovnoměrné',
            2 => 'Zrychlené',
            3 => 'Mimořádné'
        ]
    ;

    public const NAMES_SHORT =
        [
            1 => 'R',
            2 => 'Z',
            3 => 'M'
        ]
    ;

    public static function getAll(): array
    {
        return [
            self::UNIFORM,
            self::ACCELERATED,
            self::EXTRAORDINARY,
        ];
    }

    public static function getNames(): array
    {
        return self::NAMES;
    }

    private function __construct()
    {
        // empty
    }
}
