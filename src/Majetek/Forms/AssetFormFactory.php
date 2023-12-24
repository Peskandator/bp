<?php

declare(strict_types=1);

namespace App\Majetek\Forms;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Entity\AssetType;
use App\Entity\DepreciationGroup;
use App\Majetek\Action\CreateAssetAction;
use App\Majetek\Action\EditAssetAction;
use App\Majetek\Enums\AssetTypesCodes;
use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\ORM\AcquisitionRepository;
use App\Majetek\ORM\AssetTypeRepository;
use App\Majetek\ORM\CategoryRepository;
use App\Majetek\ORM\DepreciationGroupRepository;
use App\Majetek\ORM\DisposalRepository;
use App\Majetek\ORM\PlaceRepository;
use App\Majetek\Requests\CreateAssetRequest;
use App\Utils\AcquisitionsProvider;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Doctrine\Common\Collections\Collection;
use Nette\Application\UI\Form;

class AssetFormFactory
{
    private AcquisitionsProvider $acquisitionsProvider;
    private CreateAssetAction $createAssetAction;
    private AssetTypeRepository $assetTypeRepository;
    private CategoryRepository $categoryRepository;
    private DepreciationGroupRepository $depreciationGroupRepository;
    private AcquisitionRepository $acquisitionRepository;
    private PlaceRepository $placeRepository;
    private EnumerableSorter $enumerableSorter;
    private EditAssetAction $editAssetAction;
    private DisposalRepository $disposalRepository;

    public function __construct(
        AcquisitionsProvider $acquisitionsProvider,
        CreateAssetAction $createAssetAction,
        AssetTypeRepository $assetTypeRepository,
        CategoryRepository $categoryRepository,
        DepreciationGroupRepository $depreciationGroupRepository,
        AcquisitionRepository $acquisitionRepository,
        PlaceRepository $placeRepository,
        EnumerableSorter $enumerableSorter,
        EditAssetAction $editAssetAction,
        DisposalRepository $disposalRepository,
    )
    {
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->createAssetAction = $createAssetAction;
        $this->assetTypeRepository = $assetTypeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->depreciationGroupRepository = $depreciationGroupRepository;
        $this->acquisitionRepository = $acquisitionRepository;
        $this->placeRepository = $placeRepository;
        $this->enumerableSorter = $enumerableSorter;
        $this->editAssetAction = $editAssetAction;
        $this->disposalRepository = $disposalRepository;
    }
    public function create(AccountingEntity $currentEntity, bool $editing, ?Asset $asset = null): Form
    {
        $form = new Form;

        $assetTypes = $currentEntity->getAssetTypes();
        $assetTypesOptions = $this->getCollectionForSelect($assetTypes);
        $typeSelect = $form
            ->addSelect('type', 'Typ', $assetTypesOptions)
            ->setRequired(true)
        ;
        $form
            ->addInteger('inventory_number', 'Inventární číslo')
            ->addRule($form::MIN, 'Inventární číslo musí být nejméně 1', 1)
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addText('producer', 'Výrobce')
        ;
        $categories = $currentEntity->getCategories();
        $categoriesSelect = $this->getCollectionForSelect($categories);
        $form
            ->addSelect('category', 'Kategorie', $categoriesSelect)
            ->setRequired(true)
        ;

        $acquisitions = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideAcquisitions($currentEntity));
        $acquisitionsSelect = $this->getCollectionForSelectArray($acquisitions);

        $form
            ->addSelect('acquisition', 'Způsob pořízení', $acquisitionsSelect)
        ;

        $disposals = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideDisposals($currentEntity));
        $disposalsSelect = $this->getCollectionForSelectArray($disposals);
        $form
            ->addSelect('disposal', 'Způsob vyřazení', $disposalsSelect)
        ;

        $locations = $currentEntity->getLocations();
        $locationsSelect = $this->getCollectionForSelect($locations);
        $form
            ->addSelect('location', 'Středisko', $locationsSelect)
        ;

        $places = $currentEntity->getPlaces();
        $placesSelect = $this->getCollectionForSelectArray($places);
        $form
            ->addSelect('place', 'Místo', $placesSelect)
        ;

        $form
            ->addInteger('units', 'Kusů')
            ->addRule($form::MIN, 'Počet kusů musí být minimálně 1', 1)
            ->setDefaultValue(1)
        ;
        $form
            ->addCheckbox('only_tax')
            ->setDefaultValue(true)
        ;
        $form
            ->addCheckbox('has_tax_depreciations')
            ->setDefaultValue(true)
        ;
        $form
            ->addCheckbox('is_included')
            ->setDefaultValue(true)
        ;

        $assetTypesIds = $this->getAssetTypeIdsForCodes($currentEntity);
        $accountingAllowedTypes = [$assetTypesIds[AssetTypesCodes::DEPRECIABLE], $assetTypesIds[AssetTypesCodes::SMALL]];
        $taxAllowedType = $assetTypesIds[AssetTypesCodes::DEPRECIABLE];

        // Daňový box
        $depreciationGroupsTax =  $this->enumerableSorter->sortGroupsByMethodAndNumber($currentEntity->getDepreciationGroupsWithoutAccounting()->toArray());
        $depreciationGroupsTaxSelect = $this->getDepreciationGroupForSelect($depreciationGroupsTax);
        $form
            ->addSelect('group_tax', 'Odpisová skupina', $depreciationGroupsTaxSelect)
            ->addConditionOn($typeSelect, $form::EQUAL, $taxAllowedType)
            ->setRequired(true)
        ;
        $form
            ->addText('entry_price_tax', 'Daňová vstupní cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->setNullable()
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->addConditionOn($typeSelect, $form::EQUAL, $taxAllowedType)
            ->setRequired(true)
        ;
        $form
            ->addText('increased_price_tax', 'Zvýšená daňová vstupní cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->setNullable()
        ;
        $form
            ->addText('increase_date', 'Datum zvýšení VC')
            ->setNullable()
        ;
        $form
            ->addText('depreciated_amount_tax', 'Oprávky daňové')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->setNullable()
            ->addRule($form::MIN, 'Oprávky musí být minimálně 0', 0)
        ;
        $form
            ->addInteger('depreciation_year_tax', 'Pořadové číslo roku odpisu')
            ->addRule($form::MIN, 'Pořadové číslo roku odpisu musí být minimálně 0', 0)
            ->addRule($form::MAX, 'Pořadové číslo roku odpisu může být maximálně 100', 100)
            ->addConditionOn($typeSelect, $form::EQUAL, $taxAllowedType)
            ->setRequired(true)
        ;

        // Účetní box
        $depreciationGroupsAccounting = $this->enumerableSorter->sortGroupsByMethodAndNumber($currentEntity->getDepreciationGroups()->toArray());
        $depreciationGroupsAccountingSelect = $this->getDepreciationGroupForSelect($depreciationGroupsAccounting);
        $form
            ->addSelect('group_accounting', 'Odpisová skupina', $depreciationGroupsAccountingSelect)
        ;
        $form
            ->addText('entry_price_accounting', 'Účetní pořizovací cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->setNullable()
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->setNullable()
        ;
        $form
            ->addText('increased_price_accounting', 'Zvýšená účetní pořizovací cena')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Cena musí být nejméně 0', 0)
            ->setNullable()
        ;
        $form
            ->addText('increase_date_accounting', 'Datum zvýšení VC')
            ->setNullable()
        ;
        $form
            ->addText('depreciated_amount_accounting', 'Oprávky účetní')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->setNullable()
            ->addRule($form::MIN, 'Oprávky musí být minimálně 0', 0)
        ;
        $form
            ->addInteger('depreciation_year_accounting', 'Pořadové číslo roku odpisu')
            ->addRule($form::MIN, 'Pořadové číslo roku odpisu musí být minimálně 0', 0)
            ->setNullable()
        ;

        // Konec boxu

        $form
            ->addText('invoice_number', 'Doklad')
        ;
        $form
            ->addInteger('variable_symbol', 'VS')
        ;
        $form
            ->addTextArea('note', 'Poznámka')
            ->setMaxLength(600)
        ;
        $form
            ->addText('entry_date', 'Datum zařazení')
            ->setRequired(true)
        ;
        $form
            ->addText('disposal_date', 'Datum vyřazení')
            ->setNullable()
        ;

        $submitText = 'Přidat majetek';
        if ($editing) {
            $submitText = 'Uložit změny';
        }

        $form->addSubmit('send', $submitText);

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($accountingAllowedTypes) {
            if ($values->type === 0) {
                $form['type']->addError('Toto pole je povinné');
                $form->addError('Typ majetku je nutné vyplnit.');
            }

            if ($values->category === 0) {
                $form['category']->addError('Toto pole je povinné');
                $form->addError('Kategorii je nutné vyplnit.');
            }

            if (!$values->only_tax && in_array($values->type, $accountingAllowedTypes)) {
                if ($values->group_accounting === 0) {
                    $form['group_accounting']->addError('Toto pole je povinné');
                    $form->addError('Odpisovou skupinu u účetních odpisů je nutné vyplnit');
                }
                if (!$values->entry_price_accounting) {
                    $form['entry_price_accounting']->addError('Toto pole je povinné');
                    $form->addError('Vstupní cenu u účetních odpisů je nutné vyplnit');
                }
                if (!$values->depreciation_year_accounting) {
                    $form['depreciation_year_accounting']->addError('Toto pole je povinné');
                    $form->addError('Pořadové číslo roku odpisu je nutné vyplnit');
                }
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity, $editing, $asset) {
            $type = $this->assetTypeRepository->find($values->type);
            $category = $this->categoryRepository->find($values->category);
            $groupTax = $this->depreciationGroupRepository->find($values->group_tax);
            $groupAccounting = $this->depreciationGroupRepository->find($values->group_accounting);
            $place = $this->placeRepository->find($values->place);
            $acquisition = $this->acquisitionRepository->find($values->acquisition);

            $disposal = $this->disposalRepository->find($values->disposal);

            $values->increase_date = $this->changeToDateFormat($values->increase_date);
            $values->increase_date_accounting = $this->changeToDateFormat($values->increase_date_accounting);
            $values->entry_date = $this->changeToDateFormat($values->entry_date);
            $values->disposal_date = $this->changeToDateFormat($values->disposal_date);
            $today = new \DateTimeImmutable('today');

            if ($values->depreciated_amount_accounting === null) {
                $values->depreciated_amount_accounting = 0;
            }
            if ($values->depreciated_amount_tax === null) {
                $values->depreciated_amount_tax = 0;
            }

            if (!$this->isInventoryNumberAvailable($currentEntity, $values->inventory_number) && !$editing){
                $form['inventory_number']->addError('Majetek s tímto inventárním číslem již existuje');
                $form->addError('Majetek se zadaným inventárním číslem již existuje');
                return;
            }
            if (!$type || $type->getEntity()->getId() !== $currentEntity->getId()) {
                $form['type']->addError('Tento typ neexistuje');
                return;
            }
            $typeCode = $type->getCode();
            //tax box validation
            if ($typeCode === 1 && $values->has_tax_depreciations) {
                if ($groupTax === null || $groupTax->getEntity()->getId() !== $currentEntity->getId() || $groupTax->getMethod() === DepreciationMethod::ACCOUNTING) {
                    $form['group_tax']->addError('Prosím vyberte odp. skupinu');
                    $form->addError('Prosím vyberte daňovou odpisovou skupinu');
                }

                $entryPriceTax = $values->entry_price_tax;
                $increasedPriceTax = $values->increased_price_tax;
                $depreciatedAmountValidationTax = $entryPriceTax > $values->depreciated_amount_tax;
                if (!$depreciatedAmountValidationTax && $increasedPriceTax) {
                    $depreciatedAmountValidationTax = $increasedPriceTax > $values->depreciated_amount_tax;
                }
                if (!$depreciatedAmountValidationTax) {
                    $form['depreciated_amount_tax']->addError('Oprávky musí být nižší než vstupní cena.');
                    $form->addError('Oprávky musí být vyšší než vstupní cena.');
                }

                if ($increasedPriceTax) {
                    if (!$values->increase_date) {
                        $form['increase_date']->addError('Je nutné vyplnit.');
                        $form->addError('Datum zvýšení vstupní ceny je v případě vyplnění zvýšené VC nutné vyplnit');
                    } else {
                        if ($values->entry_date > $values->increase_date) {
                            $form['increase_date']->addError('Nemůže být dříve než datum zařazení');
                            $form->addError('Datum zvýšení vstupní ceny nemůže být před datumem zařazení');
                        }
                        if ($values->increase_date > $today) {
                            $form['increase_date']->addError('Datum nemůže být v budoucnosti.');
                            $form->addError('Datum zvýšení vstupní ceny nemůže být v budoucnosti.');
                        }
                    }
                }
                if ($values->increase_date && !$increasedPriceTax) {
                    $form['increased_price_tax']->addError('Je nutné vyplnit.');
                    $form->addError('Zvýšenou vstupní cenu je v případě vyplnění datumu zvýšení nutné vyplnit');
                }
            }

            //accounting box validation

            if (($typeCode === 1 || $typeCode === 3) && $values->only_tax === false) {
                if ($groupAccounting === null || $groupAccounting->getEntity()->getId() !== $currentEntity->getId()) {
                    $form['group_accounting']->addError('Prosím vyberte odp. skupinu');
                    $form->addError('Prosím vyberte účetní odp. skupinu');
                }

                $entryPriceAccounting = $values->entry_price_accounting;
                $increasedPriceAccounting = $values->increased_price_accounting;
                $depreciatedAmountValidationAccounting = $entryPriceAccounting > $values->depreciated_amount_accounting;
                if (!$depreciatedAmountValidationAccounting && $increasedPriceAccounting) {
                    $depreciatedAmountValidationAccounting = $increasedPriceAccounting > $values->depreciated_amount_accounting;
                }
                if (!$depreciatedAmountValidationAccounting) {
                    $form['depreciated_amount_accounting']->addError('Oprávky musí být nižší než vstupní cena.');
                    $form->addError('Oprávky musí být vyšší než vstupní cena.');
                }

                if ($increasedPriceAccounting) {
                    if (!$values->increase_date_accounting) {
                        $form['increase_date_accounting']->addError('Je nutné vyplnit.');
                        $form->addError('Datum zvýšení vstupní ceny je v případě vyplnění zvýšené VC nutné vyplnit');
                    } else {
                        if ($values->entry_date > $values->increase_date_accounting) {
                            $form['increase_date_accounting']->addError('Nemůže být dříve než datum zařazení');
                            $form->addError('Datum zvýšení vstupní ceny nemůže být před datumem zařazení');
                        }
                        if ($values->increase_date > $today) {
                            $form['increase_date_accounting']->addError('Datum nemůže být v budoucnosti.');
                            $form->addError('Datum zvýšení vstupní ceny nemůže být v budoucnosti.');
                        }
                    }
                }
                if (($values->increase_date_accounting) && !$increasedPriceAccounting) {
                    $form['increased_price_accounting']->addError('Je nutné vyplnit.');
                    $form->addError('Zvýšenou vstupní cenu je v případě vyplnění datumu zvýšení nutné vyplnit');
                }
            }

            // validace roků
            if ($values->entry_date > $today) {
                $form['entry_date']->addError('Datum nemůže být v budoucnosti.');
                $form->addError('Datum zařazení nemůže být v budoucnosti.');
            }
            if ($values->disposal_date) {
                if ($values->entry_date > $values->disposal_date) {
                    $form['disposal_date']->addError('Datum nemůže být před datumem zařazení');
                    $form->addError('Datum vyřazení nemůže být před datumem zařazení');
                }
            }

            if ($form->hasErrors()) {
                return;
            }

            $units = $values->units;
            if (!$units) {
                $units = 1;
            }

            $request = new CreateAssetRequest(
                $type,
                $values->name,
                $values->inventory_number,
                $values->producer,
                $category,
                $acquisition,
                $disposal,
                $place,
                $units,
                $values->only_tax,
                $values->has_tax_depreciations,
                $groupTax,
                $values->entry_price_tax,
                $values->increased_price_tax,
                $values->increase_date,
                $values->depreciated_amount_tax,
                $values->depreciation_year_tax,
                $groupAccounting,
                $values->entry_price_accounting,
                $values->increased_price_accounting,
                $values->increase_date_accounting,
                $values->depreciated_amount_accounting,
                $values->depreciation_year_accounting,
                $values->invoice_number,
                $values->variable_symbol,
                $values->entry_date,
                $values->disposal_date,
                $values->note,
                $values->is_included,
            );

            if ($editing) {
                if (!$asset) {
                    $form->getPresenter()->flashMessage('Editovaný majetek neexistuje', FlashMessageType::ERROR);
                    $form->getPresenter()->redirect(':Admin:Assets:default');
                }
                $this->editAssetAction->__invoke($currentEntity, $asset, $request);
                $form->getPresenter()->flashMessage('Majetek byl úspěšně upraven.', FlashMessageType::SUCCESS);
            } else {
                $this->createAssetAction->__invoke($currentEntity, $request);
                $form->getPresenter()->flashMessage('Majetek byl úspěšně přidán.', FlashMessageType::SUCCESS);
            }
            $form->getPresenter()->redirect(':Admin:Assets:default');
        };

        return $form;
    }

    public function fillInForm(Form $form, Asset $asset): Form
    {
        $form->setDefaults([
            'name' => $asset->getName(),
            'inventory_number' => $asset->getInventoryNumber(),
            'producer' => $asset->getProducer(),
            'units' => $asset->getUnits(),
            'only_tax' => $asset->isOnlyTax(),
            'has_tax_depreciations' => $asset->hasTaxDepreciations(),
            'is_included' => $asset->isIncluded(),
            'entry_price_tax' => $asset->getEntryPriceTax(),
            'increased_price_tax' => $asset->getIncreasedEntryPriceTax(),
            'increase_date' => $this->getDefaultDateValue($asset->getIncreaseDateTax()),
            'depreciated_amount_tax' => $asset->getDepreciatedAmountTax(),
            'depreciation_year_tax' => $asset->getDepreciationYearTax(),
            'entry_price_accounting' => $asset->getEntryPriceAccounting(),
            'increased_price_accounting' => $asset->getIncreasedEntryPriceAccounting(),
            'increase_date_accounting' => $this->getDefaultDateValue($asset->getIncreaseDateAccounting()),
            'depreciated_amount_accounting' => $asset->getDepreciatedAmountAccounting(),
            'depreciation_year_accounting' => $asset->getDepreciationYearAccounting(),
            'invoice_number' => $asset->getInvoiceNumber(),
            'variable_symbol' => $asset->getVariableSymbol(),
            'entry_date' => $this->getDefaultDateValue($asset->getEntryDate()),
            'disposal_date' => $this->getDefaultDateValue($asset->getDisposalDate()),
            'note' => $asset->getNote(),
        ]);

        $locationId = $asset->getLocation() ? $asset->getLocation()->getId() : null;
        $placeId = $asset->getPlace()?->getId();
        $acquisitionId = $asset->getAcquisition()?->getId();
        $disposalId = $asset->getDisposal()?->getId();
        $groupTaxId = $asset->getDepreciationGroupTax()?->getId();
        $groupAccountingId = $asset->getDepreciationGroupAccounting()?->getId();

        $form->setValues(array(
            'type' => $asset->getAssetType()->getId(),
            'category' => $asset->getCategory()->getId(),
            'location' => $locationId,
            'place' => $placeId,
            'acquisition' => $acquisitionId,
            'disposal' => $disposalId,
            'group_tax' => $groupTaxId,
            'group_accounting' => $groupAccountingId
        ));

        return $form;
    }

    protected function getDefaultDateValue(?\DateTimeInterface $date): string
    {
        return $date === null ? '' : $date->format('Y-m-d');
    }

    protected function getCollectionForSelectArray(array $array): array
    {
        $items = [];
        $items[0] = 'Vyberte ...';
        foreach ($array as $item) {
            $items[$item->getId()] = $item->getCode() . ' ' . $item->getName();
        }

        return $items;
    }

    protected function getCollectionForSelect(Collection $collection): array
    {
        $items = [];
        $items[0] = 'Vyberte ...';
        foreach ($collection as $item) {
                $items[$item->getId()] = $this->createSelectOptionFromItem($item);
        }

        return $items;
    }

    protected function createSelectOptionFromItem($item): string
    {
        return $item->getCode() . ' ' . $item->getName();
    }

    protected function changeToDateFormat(?string $dateTime): ?\DateTimeInterface
    {
        if ($dateTime === null) {
            return null;
        }

        return new \DateTimeImmutable($dateTime);
    }

    protected function getAssetTypeIdsForCodes(AccountingEntity $currentEntity): array
    {
        $assetTypes = $currentEntity->getAssetTypes();
        $idsForCodes = [];

        /**
         * @var AssetType $assetType
         */
        foreach ($assetTypes as $assetType) {
            $idsForCodes[$assetType->getCode()] = $assetType->getId();
        }

        return $idsForCodes;
    }

    protected function getDepreciationGroupForSelect(array $collection): array
    {
        $items = [];
        $items[0] = 'Vyberte ...';

        /**
         * @var DepreciationGroup $item
         */
        foreach ($collection as $item) {
            $items[$item->getId()] = $item->getFullName();
        }

        return $items;
    }

    protected function isInventoryNumberAvailable(AccountingEntity $currentEntity, int $number): bool
    {
        $assets = $currentEntity->getAssets();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            if ($asset->getInventoryNumber() === $number) {
                return false;
            }
        }
        return true;
    }
}
