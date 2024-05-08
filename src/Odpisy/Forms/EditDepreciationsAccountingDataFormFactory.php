<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Odpisy\Action\EditDepreciationsAccountingDataAction;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class EditDepreciationsAccountingDataFormFactory
{
    private EditDepreciationsAccountingDataAction $action;

    public function __construct(
        EditDepreciationsAccountingDataAction $action,
    )
    {
        $this->action = $action;
    }

    public function create(AccountingEntity $currentEntity, int $year, array $data): Form
    {
        $form = new Form;

        foreach ($data as $record) {
            $recordCode = $record['code'];
            $container = $form->addContainer($recordCode);
            $container
                ->addText('date_' . $recordCode, 'Datum provedení')
                ->setRequired(true)
                ->setDefaultValue($this->getDefaultDateValue($record['executionDate']))
            ;
            $container
                ->addText('account_' . $recordCode, 'Účet')
                ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
                ->setDefaultValue($record['account'])
            ;
            $container
                ->addText('debited_' . $recordCode, 'MD')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['debitedValue'])
            ;
            $container
                ->addText('credited_' . $recordCode, 'DAL')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['creditedValue'])
            ;
            $container
                ->addText('residualPrice_' . $recordCode, 'ZC')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['residualPrice'])
            ;
            $container
                ->addText('description_' . $recordCode, 'Popis')
                ->setDefaultValue($record['description'])
            ;
        }
        bdump($form);

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {

        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {
//            $request = new EditDepreciationRequest(
//                $values->id,
//                $values->amount,
//                $values->percentage,
//                $values->executable,
//            );
//            $this->action->__invoke();
            $form->getPresenter()->flashMessage(
                'Odpis byl úspěšně upraven. Neprovedené odpisy následujících let byly přepočítány.',
                FlashMessageType::SUCCESS)
            ;
            $form->getPresenter()->redirect('this');
        };

        return $form;
    }

    protected function getDefaultDateValue(?\DateTimeInterface $date): string
    {
        return $date === null ? '' : $date->format('Y-m-d');
    }
}