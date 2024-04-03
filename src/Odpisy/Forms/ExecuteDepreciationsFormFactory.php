<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Odpisy\Action\ExecuteDepreciationsAction;
use App\Utils\DateTimeFormatter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class ExecuteDepreciationsFormFactory
{
    private ExecuteDepreciationsAction $executeDepreciationsAction;
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(
        ExecuteDepreciationsAction $executeDepreciationsAction,
        DateTimeFormatter $dateTimeFormatter,
    )
    {
        $this->executeDepreciationsAction = $executeDepreciationsAction;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function create(AccountingEntity $currentEntity, array $data): Form
    {
        $form = new Form;

        $form->addSubmit('send');

        $form
            ->addText('execution_date', 'Datum zvýšení VC')
            ->setNullable()
        ;

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity, $data) {
            $this->executeDepreciationsAction->__invoke($currentEntity, $data, $this->dateTimeFormatter->changeToDateFormat($values->execution_date));
            $form->getPresenter()->flashMessage('Odpisy byly provedeny. Pohyby byly vytvořeny.', FlashMessageType::SUCCESS);
            $form->getPresenter()->redirect('this');
        };

        return $form;
    }
}