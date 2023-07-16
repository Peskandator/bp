<?php

namespace App\Majetek\Requests;

use App\Entity\DepreciationGroup;

class CreateCategoryRequest
{
    public function __construct(
        public int $code,
        public ?string $name,
        public ?DepreciationGroup $depreciationGroup,
        public ?string $accountAsset,
        public ?string $accountDepreciation,
        public ?string $accountRepairs
    ) {
    }
}
