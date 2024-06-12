<?php

namespace App\Reports\Forms;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationGroup;
use App\Entity\Place;
use App\Reports\Enums\AssetColumns;
use App\Reports\Enums\DepreciationColumns;
use App\Utils\DateTimeFormatter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class FilterDepreciationsForReportFormFactory
{
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
    )
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function create(AccountingEntity $currentEntity): Form
    {
        $form = new Form;

        $columns = DepreciationColumns::NAMES;
        $form
            ->addCheckboxList('columns', 'Sloupce', $columns)
            ->setDefaultValue(array_keys($columns))
        ;
        $sorting = DepreciationColumns::SORTING_BY;
        $form
            ->addRadioList('sorting', 'Třídění', $sorting)
            ->setDefaultValue('inventory_number')
        ;
        $summing = DepreciationColumns::SUMMING_BY;
        $form
            ->addCheckboxList('summing', 'Sumy', $summing)
        ;
        $grouping = DepreciationColumns::GROUPING_BY;
        $form
            ->addRadioList('grouping', 'Seskupení', $grouping)
            ->setDefaultValue('none')
        ;
        $form
            ->addText('from_date', 'Datum provedení od')
            ->setNullable()
        ;
        $form
            ->addText('to_date', 'Datum provedení do')
            ->setNullable()
        ;
        $categories = $this->getCategoriesForCheckboxList($currentEntity->getCategories()->toArray());
        $form
            ->addCheckboxList('categories', 'Kategorie', $categories)
        ;
        $form
            ->addText('entry_price_from', 'do VC')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být vyšší než 0.',0)
            ->setNullable()
        ;
        $form
            ->addText('entry_price_to', 'od VC')
            ->addRule($form::FLOAT,'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být vyšší než 0.', 0)
            ->setNullable()
        ;
        $form
            ->addText('depreciation_amount_from', 'do VC')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být vyšší než 0.',0)
            ->setNullable()
        ;
        $form
            ->addText('depreciation_amount_to', 'od VC')
            ->addRule($form::FLOAT,'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být vyšší než 0.', 0)
            ->setNullable()
        ;
        $form
            ->addInteger('account_debited_from', 'Účet MD')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->setNullable()
        ;
        $form
            ->addInteger('account_debited_to', 'Účet MD')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->setNullable()
        ;
        $form
            ->addInteger('account_credited_from', 'Účet DAL')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->setNullable()
        ;
        $form
            ->addInteger('account_credited_to', 'Účet DAL')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->setNullable()
        ;

        $depreciationGroups = $this->getDepreciationGroupsForCheckboxList($currentEntity->getDepreciationGroups()->toArray());
        $form
            ->addCheckboxList('depreciation_groups', 'Odpisové skupiny', $depreciationGroups)
        ;
        $form
            ->addCheckbox('only_accountable', 'Nezaúčtovatelné')
        ;

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            if (((float)$values->entry_price_from && (float)$values->entry_price_to) && (float)$values->entry_price_from > (float)$values->entry_price_to) {
                $errorMsg = 'Pole "Od VC" musí být vyšší než pole "Do VC"';
                $form['entry_price_from']->addError($errorMsg);
                $form['entry_price_to']->addError($errorMsg);
                $form->getPresenter()->flashMessage($errorMsg,FlashMessageType::ERROR);
            }
            if (((float)$values->depreciation_amount_from !== null && (float)$values->depreciation_amount_to) !== null && (float)$values->depreciation_amount_from > (float)$values->depreciation_amount_to) {
            $errorMsg = 'Pole "Od částky" musí být vyšší než pole "Do částky"';
                $form['depreciation_amount_from']->addError($errorMsg);
                $form['depreciation_amount_to']->addError($errorMsg);
                $form->getPresenter()->flashMessage($errorMsg,FlashMessageType::ERROR);
            }

            $fromDate = $this->dateTimeFormatter->changeToDateFormat($values->from_date);
            $toDate = $this->dateTimeFormatter->changeToDateFormat($values->to_date);
            if ($fromDate && $toDate && $fromDate > $toDate) {
                $errorMsg = 'Neplatný vyhledávací dotaz. Datum "od" musí dříve než datum "do".';
                $form['from_date']->addError($errorMsg);
                $form->getPresenter()->flashMessage($errorMsg, FlashMessageType::ERROR);
            }

            foreach ($values->summing as $sumByColumn) {
                if (!in_array($sumByColumn, $values->columns)) {
                    $errorMsg = 'Sloupec "' . AssetColumns::SUMMING_BY[$sumByColumn] . '", podle kterého jsou počítány sumy, musí být zaškrtnut v seznamu sloupců.';
                    $form['summing']->addError($errorMsg);
                    $form->getPresenter()->flashMessage($errorMsg, FlashMessageType::ERROR);
                }
            }

            if (count($values->columns) <= 0) {
                $errorMsg = 'Výsledek musí obsahovat alespoň jeden sloupec.';
                $form['columns']->addError($errorMsg);
                $form->getPresenter()->flashMessage($errorMsg, FlashMessageType::ERROR);
            }

            if ($values->account_debited_to && $values->account_debited_from && $values->account_debited_to < $values->account_debited_from) {
                $from = $values->account_debited_to;
                $values->account_debited_to = $values->account_debited_from;
                $values->account_debited_from = $from;
            }
            if ($values->account_credited_to && $values->account_credited_from && $values->account_credited_to < $values->account_credited_from) {
                $from = $values->account_credited_to;
                $values->account_credited_to = $values->account_credited_from;
                $values->account_credited_from = $from;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $valuesArr = json_decode(json_encode($values), true);
            $data = urlencode(json_encode($valuesArr));
            $form->getPresenter()->redirect('DepreciationReports:result', $data);
        };

        return $form;
    }

    private function getCategoriesForCheckboxList(array $categories): array
    {
        $arr = [];
        /**
         * @var Place $category
         */
        foreach ($categories as $category) {
            $arr[$category->getId()] = $category->getName();
        }

        return $arr;
    }

    private function getDepreciationGroupsForCheckboxList(array $groups): array
    {
        $arr = [];
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            $arr[$group->getId()] = $group->getFullName();
        }

        return $arr;
    }
}
