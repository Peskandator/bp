<?php


declare(strict_types=1);

namespace App\Majetek\Enums;

final class AssetTypesCodes
{
    public const DEPRECIABLE = 1;
    public const NONDEPRECIABLE = 2;
    public const SMALL = 3;
    public const LEASING = 4;

    private function __construct()
    {
        // empty
    }
}
