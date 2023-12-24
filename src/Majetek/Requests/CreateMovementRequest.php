<?php

namespace App\Majetek\Requests;

use App\Entity\Asset;
use App\Entity\DepreciationAccounting;
use App\Entity\DepreciationTax;

class CreateMovementRequest
{
    public function __construct(
        public Asset $asset,
        public \DateTimeInterface $date,
        public int $type,
        public float $value,
        public string $description,
        public string $accountDebited,
        public string $accountCredited,
        public ?DepreciationTax $depreciationTax,
        public ?DepreciationAccounting $depreciationAccounting,
    ) {
    }
}
