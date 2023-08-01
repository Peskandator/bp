<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\DepreciationGroup;
use App\Presenters\BaseAdminPresenter;
use App\Utils\AcquisitionsProvider;
use App\Utils\FlashMessageType;
use Doctrine\Common\Collections\Collection;
use Nette\Application\UI\Form;

final class AssetsPresenter extends BaseAdminPresenter
{
    private AcquisitionsProvider $acquisitionsProvider;

    public function __construct(
        AcquisitionsProvider $acquisitionsProvider
    )
    {
        parent::__construct();
        $this->acquisitionsProvider = $acquisitionsProvider;
    }

    public function actionDefault(): void
    {
        $this->template->assets = $this->currentEntity->getAssets();
    }

    public function actionCreate(): void
    {
        $this->template->depreciationGroups = $this->currentEntity->getDepreciationGroups();
        $this->template->categories = $this->currentEntity->getCategories();
        $this->template->acquisitions = $this->acquisitionsProvider->provideAcquisitions($this->currentEntity);
        $this->template->locations = $this->currentEntity->getLocations();
        $this->template->places = $this->currentEntity->getPlaces();
        $this->template->disposals = $this->currentEntity->getDisposals();
        $this->template->assetTypes = $this->currentEntity->getAssetTypes();
    }

    protected function createComponentCreateAssetForm(): Form
    {
        $form = new Form;

        $assetTypes = $this->currentEntity->getAssetTypes();
        $assetTypesSelect = $this->getCollectionForSelect($assetTypes);
        $form
            ->addSelect('type', 'Typ', $assetTypesSelect)
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addText('producer', 'Výrobce')
        ;
        $categories = $this->currentEntity->getCategories();
        $categoriesSelect = $this->getCollectionForSelect($categories);
        $form
            ->addSelect('category', 'Kategorie', $categoriesSelect)
            ->setRequired(true)
        ;

        $acquisitions = $this->currentEntity->getAcquisitions();
        $acquisitionsSelect = $this->getCollectionForSelect($acquisitions);
        $form
            ->addSelect('acquisition', 'Způsob pořízení', $acquisitionsSelect)
        ;

        $disposals = $this->currentEntity->getDisposals();
        $disposalsSelect = $this->getCollectionForSelect($disposals);
        $form
            ->addSelect('disposal', 'Způsob vyřazení', $disposalsSelect)
        ;

        $locations = $this->currentEntity->getLocations();
        $locationsSelect = $this->getCollectionForSelect($locations);
        $form
            ->addSelect('location', 'Středisko', $locationsSelect)
        ;

        $places = $this->currentEntity->getPlaces();
        $placesSelect = $this->getCollectionForSelectArray($places);
        $form
            ->addSelect('place', 'Místo', $placesSelect)
        ;

        $form
            ->addInteger('units', 'Kusů')
            ->addRule($form::MIN, 'Počet kusů musí být minimálně 1')
        ;

        $isOnlyTax = $form->addCheckbox('only_tax');

        $depreciationGroups = $this->currentEntity->getDepreciationGroups();
        $depreciationGroupsSelect = $this->getDepreciationGroupForSelect($depreciationGroups);

        // Daňový box
        $form
            ->addSelect('group_tax', 'Odpisová skupina', $depreciationGroupsSelect)
            ->setRequired(true)
        ;
        $form
            ->addText('entry_price_tax', 'Daňová vstupní cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->setRequired(true)
        ;
        $form
            ->addText('increased_price_tax', 'Zvýšená daňová vstupní cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->setRequired(true)
        ;
        $form
            ->addText('depreciated_amount_tax', 'Oprávky daňové')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Oprávky musí být minimálně 0', 0)
            ->setRequired(true)
        ;
        $form
            ->addInteger('depreciation_year_tax', 'Rok odpisu')
            ->addRule($form::MIN, 'Rok odpisu musí být minimálně 0')
            ->setRequired(true)
        ;
        $form
            ->addInteger('depreciation_increased_year_tax', 'Rok odpisu ze zvýšené ceny')
            ->addRule($form::MIN, 'Rok odpisu musí být minimálně 0')
            ->setRequired(true)
        ;

        // Účetní box
        $form
            ->addSelect('group_accounting', 'Odpisová skupina', $depreciationGroupsSelect)
            ->addConditionOn($isOnlyTax, $form::EQUAL, false)
            ->setRequired(true)
        ;
        $form
            ->addText('entry_price_accounting', 'Účetní pořizovací cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->addConditionOn($isOnlyTax, $form::EQUAL, false)
            ->setRequired(true)
        ;
        $form
            ->addText('increased_price_accounting', 'Zvýšená účetní pořizovací cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->addConditionOn($isOnlyTax, $form::EQUAL, false)
            ->setRequired(true)
        ;
        $form
            ->addText('depreciated_amount_accounting', 'Oprávky účetní')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Oprávky musí být minimálně 0', 0)
            ->addConditionOn($isOnlyTax, $form::EQUAL, false)
            ->setRequired(true)
        ;

        // Konec boxu

        $form
            ->addInteger('invoice_number', 'Evidenční číslo')
            ->setRequired(true)
        ;
        $form
            ->addText('variable_symbol', 'VS')
            ->setRequired(true)
        ;
        // TODO - datepicker dates
        $form
            ->addText('entry_date', 'Datum zařazení')
            ->setRequired(true);
        ;
        $form
            ->addText('disposal_date', 'Datum vyřazení')
            ->setRequired(true);
        ;

        $form->addSubmit('send', 'Přidat majetek');

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            bdump($values);
//            $request = new CreateEntityRequest(
//                $values->name,
//                $values->company_id,
//                $values->country,
//                $values->city,
//                $values->zip_code,
//                $values->street
//            );
//            $newEntityId = $this->createEntityAction->__invoke($request);
            $this->flashMessage('Majetek byl úspěšně přidán.', FlashMessageType::SUCCESS);
            $this->redirect(':Admin:Assets:default');
        };

        return $form;
    }


    protected function getCollectionForSelectArray(array $array): array
    {
        $items = [];
        foreach ($array as $item) {
            $items[$item->getId()] = $item->getCode() . ' ' . $item->getName();
        }

        return $items;
    }

    protected function getCollectionForSelect(Collection $collection): array
    {
        $items = [];
        foreach ($collection as $item) {
            $items[$item->getId()] = $item->getCode() . ' ' . $item->getName();
        }

        return $items;
    }

    protected function getDepreciationGroupForSelect(Collection $collection): array
    {
        $items = [];
        /**
         * @var DepreciationGroup $item
         */
        foreach ($collection as $item) {
            $items[$item->getId()] = $item->getFullShortName();
        }

        return $items;
    }
}