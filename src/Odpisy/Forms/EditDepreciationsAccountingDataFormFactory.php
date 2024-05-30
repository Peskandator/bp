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

        $defaultOrigin = $accountingData->getOrigin() ?? 'O-HIM';
        $form
            ->addText('origin')
            ->setDefaultValue($defaultOrigin)
            ->setNullable()
        ;
        $form
            ->addInteger('operation_month')
            ->setDefaultValue($accountingData->getOperationMonth())
            ->addRule($form::MAX, 'Období musí být v rozmezí 1-12', 12)
            ->addRule($form::MIN, 'Období musí být v rozmezí 1-12', 1)
            ->setNullable()
        ;
        $form
            ->addInteger('document')
            ->setDefaultValue($accountingData->getDocument())
            ->addRule($form::MAX, 'Může být pouze šestimístné číslo.', 1000000)
            ->setNullable()
        ;
        $form
            ->addText('operation_date')
            ->setDefaultValue($this->getDefaultDateValue($accountingData->getOperationDate()))
            ->setType('date')
            ->setNullable()
        ;

        foreach ($data as $record) {
            $recordCode = $record['code'];
            $container = $form->addContainer($recordCode);
            $container
                ->addText('execution_date', 'Datum provedení')
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
                ->addText('description', 'Popis')
                ->setDefaultValue($record['description'])
                ->addRule($form::MAX_LENGTH, 'Lze zadat pouze 40 znaků.', 40)
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

    protected function getDefaultDateValue(?\DateTimeInterface $date): string
    {
        return $date === null ? '' : $date->format('Y-m-d');
    }
}