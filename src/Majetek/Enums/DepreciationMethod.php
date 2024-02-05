<?php

declare(strict_types=1);

namespace App\Majetek\Enums;

final class DepreciationMethod
{
    public const UNIFORM = 1;
    public const ACCELERATED = 2;
    public const EXTRAORDINARY= 3;
    public const ACCOUNTING = 4;
    public const NAMES =
        [
            1 => 'Rovnoměrný',
            2 => 'Zrychlený',
            3 => 'Mimořádný',
            4 => 'Účetní'
        ]
    ;

    public const NAMES_SHORT =
        [
            1 => 'R',
            2 => 'Z',
            3 => 'M',
            4 => 'U'
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

    public static function getNamesForSelect(): array
    {
        $names = self::NAMES;
        $selectArr = [];
        $selectArr[0] = 'Select...';

        $selectArr[1] = $names[1];
        $selectArr[2] = $names[2];
        $selectArr[3] = $names[3];
        $selectArr[4] = $names[4];

        return $selectArr;
    }

    private function __construct()
    {
        // empty
    }
}
