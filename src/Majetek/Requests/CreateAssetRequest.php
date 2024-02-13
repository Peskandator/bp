<?php

namespace App\Majetek\Requests;

use App\Entity\Acquisition;
use App\Entity\AssetType;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\Disposal;
use App\Entity\Location;
use App\Entity\Place;

class CreateAssetRequest
{
    public function __construct(
        public AssetType $type,
        public string $name,
        public int $inventoryNumber,
        public ?string $producer,
        public Category $category,
        public ?Acquisition $acquisition,
        public ?Disposal $disposal,
        public ?Location $location,
        public ?Place $place,
        public ?int $units,
        public bool $onlyTax,
        public bool $hasTaxDepreciations,
        public ?DepreciationGroup $depreciationGroupTax,
        public ?float $entryPrice,
        public ?float $increasedEntryPrice,
        public ?\DateTimeInterface $increaseDate,
        public ?float $depreciatedAmountTax,
        public ?int $depreciationYearTax,
        public ?DepreciationGroup $depreciationGroupAccounting,
        public ?float $depreciatedAmountAccounting,
        public ?int $depreciationYearAccounting,
        public ?string $invoiceNumber,
        public ?int $variableSymbol,
        public ?\DateTimeInterface $entryDate,
        public ?\DateTimeInterface $disposalDate,
        public ?string $note,
        public bool $isIncluded,
    ) {
    }
}
