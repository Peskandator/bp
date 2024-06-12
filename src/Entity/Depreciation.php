<?php

declare(strict_types=1);

namespace App\Entity;

use App\Odpisy\Requests\UpdateDepreciationRequest;

interface Depreciation
{
    public function getId(): int;
    public function getDepreciationAmount(): float;
    public function getDepreciatedAmount(): float;
    public function getEntryPrice(): float;
    public function getIncreasedEntryPrice(): ?float;
    public function getResidualPrice(): float;
    public function getAsset(): Asset;
    public function getDepreciationYear(): int;
    public function isAccountingDepreciation(): bool;
    public function isExecutable(): bool;
    public function getYear(): ?int;
    public function getDepreciationGroup(): DepreciationGroup;
    public function isExecutionCancelable(): bool;
    public function updateFromRequest(UpdateDepreciationRequest $request): void;
    public function getRate(): ?float;
    public function getRateFormat(): int;
    public function getPercentage(): float;
}