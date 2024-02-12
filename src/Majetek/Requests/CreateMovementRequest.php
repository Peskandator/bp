<?php

namespace App\Majetek\Requests;

use App\Entity\Asset;

class CreateMovementRequest
{
    public function __construct(
        public Asset $asset,
        public int $type,
        public float $value,
        public ?float $residualPrice,
        public string $description,
        public string $accountDebited,
        public string $accountCredited,
        public \DateTimeInterface $executionDate
    ) {
    }
}
