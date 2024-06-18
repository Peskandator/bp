<?php

declare(strict_types=1);

namespace App\Utils;

class PriceFilter
{
    public function __invoke(?float $number): string
    {
        if ($number !== null) {
            return str_replace('.', ',', strval($number)) . ' Kč';
        }
        return '';
    }
}
