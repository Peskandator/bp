<?php

namespace App\Majetek\Requests;

use App\Entity\DepreciationGroup;

class CreateDepreciationGroupRequest
{
    public function __construct(
        public int $method,
        public int $group,
        public ?int $years,
        public ?int $months,
        public bool $isCoefficient,
        public float $rateFirstYear,
        public float $rate,
        public float $rateIncreasedPrice,
    ) {
    }
}
