<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationsAccountingData;
use App\Odpisy\Action\EditDepreciationsAccountingDataAction;
use App\Utils\FlashMessageType;
use DateTime;
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

    public function create(DepreciationsAccountingData $accountingData): Form
    {
        $form = new Form;

        $data = $accountingData->getArrayData();

        $form
            ->addHidden('export')
            ->setNullable()
        ;

        foreach ($data as $record) {
            $recordCode = $record['code'];
            $container = $form->addContainer($recordCode);
            $container
                ->addText('date', 'Datum provedení')
                ->setRequired(true)
                ->setDefaultValue($record['executionDate'])
                ->setType('date')
            ;
            $container
                ->addText('account', 'Účet')
                ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
                ->setDefaultValue($record['account'])
            ;
            $container
                ->addText('debited', 'MD')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['debitedValue'])
            ;
            $container
                ->addText('credited', 'DAL')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['creditedValue'])
            ;
            $container
                ->addText('residualPrice', 'ZC')
                ->addRule($form::FLOAT, 'Zadejte číslo')
                ->setNullable()
                ->setDefaultValue($record['residualPrice'])
            ;
            $container
                ->addText('description', 'Popis')
                ->setDefaultValue($record['description'])
            ;
        }

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($accountingData) {

        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($accountingData) {
            $year = $accountingData->getYear();

            $valuesArray = json_decode(json_encode($values), true);
            $this->action->__invoke($accountingData, $valuesArray);
            $form->getPresenter()->flashMessage(
                'Data byla uložena.',
                FlashMessageType::SUCCESS)
            ;
            if (!$values->export) {
                $form->getPresenter()->redirect('this');
            }
            if ($values->export === 'excel') {
                $form->getPresenter()->redirect(':Admin:Accounting:exportXlsx', $year);
            }
            if ($values->export === 'dbf') {
                $form->getPresenter()->redirect(':Admin:Accounting:exportDbf', $year);
            }
        };

        return $form;
    }
}