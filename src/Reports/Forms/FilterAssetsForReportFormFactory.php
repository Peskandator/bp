<?php

namespace App\Reports\Forms;

use App\Entity\AccountingEntity;
use App\Entity\Place;
use App\Majetek\Enums\AssetColumns;
use App\Majetek\Enums\AssetTypesCodes;
use App\Utils\DateTimeFormatter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class FilterAssetsForReportFormFactory
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

        $columns = AssetColumns::NAMES;
        $form
            ->addCheckboxList('columns', 'Sloupce', $columns)
            ->setDefaultValue(array_keys($columns))
        ;
        $sorting = AssetColumns::SORTING_BY;
        $form
            ->addRadioList('sorting', 'Třídění', $sorting)
            ->setDefaultValue('inventory_number')
        ;
        $summing = AssetColumns::SUMMING_BY;
        $form
            ->addCheckboxList('summing', 'Sumy', $summing)
        ;
        $grouping = AssetColumns::GROUPING_BY;
        $form
            ->addRadioList('grouping', 'Seskupení', $grouping)
            ->setDefaultValue('none')
        ;
        $assetTypes = AssetTypesCodes::NAMES;
        $form
            ->addCheckboxList('types', 'Typy majetku', $assetTypes)
        ;
        $form
            ->addText('from_date', 'Datum zařazení od')
            ->setNullable()
        ;
        $form
            ->addText('to_date', 'Datum zařazení do')
            ->setNullable()
        ;
        $places = $this->getPlacesForCheckboxList($currentEntity->getPlaces());
        $form
            ->addCheckboxList('places', 'Typy majetku', $places)
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
            ->addCheckbox('disposed', 'Vyřazené')
        ;

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            if (((float)$values->entry_price_from && (float)$values->entry_price_to) && (float)$values->entry_price_from > (float)$values->entry_price_to) {
                $errorMsg = 'Pole "Od VC" musí být vyšší než pole "Do VC"';
                $form['entry_price_from']->addError($errorMsg);
                $form['entry_price_to']->addError($errorMsg);
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
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {

            $valuesArr = json_decode(json_encode($values), true);
            $data = urlencode(json_encode($valuesArr));
            $form->getPresenter()->redirect('AssetReports:result', $data);
        };

        return $form;
    }

    private function getPlacesForCheckboxList(array $places): array
    {
        $arr = [];
        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            $arr[$place->getId()] = $place->getName();
        }

        return $arr;
    }

    private function getCategoriesForCheckboxList(array $cateogories): array
    {
        $arr = [];
        /**
         * @var Place $category
         */
        foreach ($cateogories as $category) {
            $arr[$category->getId()] = $category->getName();
        }

        return $arr;
    }
}