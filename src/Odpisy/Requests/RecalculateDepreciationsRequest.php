<?php

namespace App\Odpisy\Requests;

use App\Entity\Asset;
use App\Entity\DepreciationGroup;

class RecalculateDepreciationsRequest
{
    public function __construct(
        public Asset $asset,
        public DepreciationGroup $group,
        public int $depreciationYear,
        public int $year,
        public ?int $disposalYear,
        public ?int $totalDepreciationYears,
        public float $entryPrice,
        public float $correctEntryPrice,
        public float $residualPrice,
        public float $depreciatedAmount,
        public int $rateFormat,
        public ?\DateTimeInterface $increaseDate,
    ) {
    }
}

