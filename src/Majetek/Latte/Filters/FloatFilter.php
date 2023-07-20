<?php

declare(strict_types=1);

namespace App\Majetek\Latte\Filters;

class FloatFilter
{
    public function __invoke(?float $number): string
    {
        return str_replace('.', ',', strval($number));
    }
}
