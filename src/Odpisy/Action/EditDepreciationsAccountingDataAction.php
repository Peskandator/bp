<?php

namespace App\Odpisy\Action;


use App\Entity\DepreciationsAccountingData;

class EditDepreciationsAccountingDataAction
{

    public function __construct(
    ) {
    }

    public function __invoke(DepreciationsAccountingData $accountingData, $records): void
    {


//        $accountingData->update();
    }
}
