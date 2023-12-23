<?php

namespace App\Majetek\Requests;

class CreateDepreciationGroupRequest
{
    public function __construct(
        public int $method,
        public ?int $group,
        public ?string $prefix,
        public ?int $years,
        public ?int $months,
        public int $rateFormat,
        public ?float $rateFirstYear,
        public ?float $rate,
        public ?float $rateIncreasedPrice,
    ) {
    }
}
