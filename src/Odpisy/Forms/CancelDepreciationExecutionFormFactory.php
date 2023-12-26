<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Odpisy\Action\CancelDepreciationExecutionAction;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class CancelDepreciationExecutionFormFactory
{
    private CancelDepreciationExecutionAction $cancelDepreciationExecutionAction;

    public function __construct(
        CancelDepreciationExecutionAction $cancelDepreciationExecutionAction
    ) {
        $this->cancelDepreciationExecutionAction = $cancelDepreciationExecutionAction;
    }


    public function create(AccountingEntity $currentEntity, int $year): Form
    {
        $form = new Form;

        $form->addSubmit('send');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity, $year) {
            $this->cancelDepreciationExecutionAction->__invoke($currentEntity, $year);
            $form->getPresenter()->flashMessage('Provedení odpisů bylo zrušeno. Související pohyby byly vymazány.', FlashMessageType::SUCCESS);
            $form->getPresenter()->redirect('this');
        };

        return $form;
    }
}