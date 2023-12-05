<?php

namespace App\Odpisy\Requests;

use App\Entity\Asset;
use App\Entity\DepreciationGroup;

class CreateDepreciationRequest
{
    public function __construct(
        public Asset $asset,
        public DepreciationGroup $depreciationGroup,
        public int $year,
        public int $depreciationYear,
        public float $depreciationAmount,
        public float $percentage,
        public float $depreciatedAmount,
        public float $residualPrice,
        public bool $executable,
        public float $rate,
    ) {
    }
}

