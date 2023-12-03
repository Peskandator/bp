<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationAccounting;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Requests\EditDepreciationRequest;

class EditAccountingDepreciationAction
{
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        EditDepreciationCalculator $editDepreciationCalculator
    ) {
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function __invoke(DepreciationAccounting $depreciation, EditDepreciationRequest $request): void
    {
        $this->editDepreciationCalculator->recalculateAccountingDepreciationsAfterEditingDepreciation($depreciation, $request);
    }
}
