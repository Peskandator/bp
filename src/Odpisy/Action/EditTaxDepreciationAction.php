<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationTax;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Requests\EditDepreciationRequest;

class EditTaxDepreciationAction
{
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        EditDepreciationCalculator $editDepreciationCalculator,
    ) {
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function __invoke(DepreciationTax $depreciation, EditDepreciationRequest $request): void
    {
        $this->editDepreciationCalculator->recalculateTaxDepreciationsAfterEditingDepreciation($depreciation, $request);
    }
}
