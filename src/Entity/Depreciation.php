<?php

declare(strict_types=1);

namespace App\Entity;

interface Depreciation
{
    public function getDepreciationAmount(): float;
    public function getAsset(): Asset;
    public function getDepreciationYear(): int;
    public function isAccountingDepreciation(): bool;
    public function isExecutable(): bool;
    public function getYear(): ?int;
}