<?php

declare(strict_types=1);

namespace App\Presenters\Admin;

use App\Entity\AccountingEntity;
use App\Entity\DepreciationGroup;
use App\Entity\Location;
use App\Majetek\Action\AddAcquisitionAction;
use App\Majetek\Action\AddCategoryAction;
use App\Majetek\Action\AddDepreciationGroupAction;
use App\Majetek\Action\AddLocationAction;
use App\Majetek\Action\AddPlaceAction;
use App\Majetek\Action\DeleteAcquisitionAction;
use App\Majetek\Action\DeleteCategoryAction;
use App\Majetek\Action\DeleteDepreciationGroupAction;
use App\Majetek\Action\DeleteLocationAction;
use App\Majetek\Action\DeletePlaceAction;
use App\Majetek\Action\EditAcquisitionAction;
use App\Majetek\Action\EditAssetTypeAction;
use App\Majetek\Action\EditCategoryAction;
use App\Majetek\Action\EditDepreciationGroupAction;
use App\Majetek\Action\EditDisposalAction;
use App\Majetek\Action\EditLocationAction;
use App\Majetek\Action\EditPlaceAction;
use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\ORM\AcquisitionRepository;
use App\Majetek\ORM\AssetTypeRepository;
use App\Majetek\ORM\CategoryRepository;
use App\Majetek\ORM\DepreciationGroupRepository;
use App\Majetek\ORM\DisposalRepository;
use App\Majetek\ORM\LocationRepository;
use App\Majetek\ORM\PlaceRepository;
use App\Majetek\Requests\CreateCategoryRequest;
use App\Majetek\Requests\CreateDepreciationGroupRequest;
use App\Presenters\BaseAdminPresenter;
use App\Utils\AcquisitionsProvider;
use App\Utils\DialsCodeValidator;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;


final class DialsPresenter extends BaseAdminPresenter
{
    private AddLocationAction $addLocationAction;
    private AddAcquisitionAction $addAcquisitionAction;
    private AddPlaceAction $addPlaceAction;
    private AcquisitionsProvider $acquisitionsProvider;
    private EditAcquisitionAction $editAcquisitionAction;
    private AcquisitionRepository $acquisitionRepository;
    private DialsCodeValidator $dialsCodeValidator;
    private PlaceRepository $placeRepository;
    private LocationRepository $locationRepository;
    private EditLocationAction $editLocationAction;
    private EditPlaceAction $editPlaceAction;
    private DeletePlaceAction $deletePlaceAction;
    private DeleteAcquisitionAction $deleteAcquisitionAction;
    private DeleteLocationAction $deleteLocationAction;
    private AddCategoryAction $addCategoryAction;
    private CategoryRepository $categoryRepository;
    private DepreciationGroupRepository $depreciationGroupRepository;
    private DeleteCategoryAction $deleteCategoryAction;
    private DeleteDepreciationGroupAction $deleteDepreciationGroupAction;
    private AssetTypeRepository $assetTypeRepository;
    private EditAssetTypeAction $editAssetTypeAction;
    private AddDepreciationGroupAction $addDepreciationGroupAction;
    private EditDepreciationGroupAction $editDepreciationGroupAction;
    private EditCategoryAction $editCategoryAction;
    private EnumerableSorter $enumerableSorter;
    private DisposalRepository $disposalRepository;
    private EditDisposalAction $editDisposalAction;

    public function __construct(
        AddLocationAction $addLocationAction,
        AddAcquisitionAction $addAcquisitionAction,
        AddPlaceAction $addPlaceAction,
        AcquisitionsProvider $acquisitionsProvider,
        EditAcquisitionAction $editAcquisitionAction,
        AcquisitionRepository $acquisitionRepository,
        DialsCodeValidator $dialsCodeValidator,
        PlaceRepository $placeRepository,
        LocationRepository $locationRepository,
        EditLocationAction $editLocationAction,
        EditPlaceAction $editPlaceAction,
        DeletePlaceAction $deletePlaceAction,
        DeleteAcquisitionAction $deleteAcquisitionAction,
        DeleteLocationAction $deleteLocationAction,
        AddCategoryAction $addCategoryAction,
        CategoryRepository $categoryRepository,
        DepreciationGroupRepository $depreciationGroupRepository,
        DeleteCategoryAction $deleteCategoryAction,
        DeleteDepreciationGroupAction $deleteDepreciationGroupAction,
        AssetTypeRepository $assetTypeRepository,
        EditAssetTypeAction $editAssetTypeAction,
        AddDepreciationGroupAction $addDepreciationGroupAction,
        EditDepreciationGroupAction $editDepreciationGroupAction,
        EditCategoryAction $editCategoryAction,
        EnumerableSorter $enumerableSorter,
        DisposalRepository $disposalRepository,
        EditDisposalAction $editDisposalAction
    )
    {
        parent::__construct();
        $this->addLocationAction = $addLocationAction;
        $this->addAcquisitionAction = $addAcquisitionAction;
        $this->addPlaceAction = $addPlaceAction;
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->editAcquisitionAction = $editAcquisitionAction;
        $this->acquisitionRepository = $acquisitionRepository;
        $this->dialsCodeValidator = $dialsCodeValidator;
        $this->placeRepository = $placeRepository;
        $this->locationRepository = $locationRepository;
        $this->editLocationAction = $editLocationAction;
        $this->editPlaceAction = $editPlaceAction;
        $this->deletePlaceAction = $deletePlaceAction;
        $this->deleteAcquisitionAction = $deleteAcquisitionAction;
        $this->deleteLocationAction = $deleteLocationAction;
        $this->addCategoryAction = $addCategoryAction;
        $this->categoryRepository = $categoryRepository;
        $this->depreciationGroupRepository = $depreciationGroupRepository;
        $this->deleteCategoryAction = $deleteCategoryAction;
        $this->deleteDepreciationGroupAction = $deleteDepreciationGroupAction;
        $this->assetTypeRepository = $assetTypeRepository;
        $this->editAssetTypeAction = $editAssetTypeAction;
        $this->addDepreciationGroupAction = $addDepreciationGroupAction;
        $this->editDepreciationGroupAction = $editDepreciationGroupAction;
        $this->editCategoryAction = $editCategoryAction;
        $this->enumerableSorter = $enumerableSorter;
        $this->disposalRepository = $disposalRepository;
        $this->editDisposalAction = $editDisposalAction;
    }

    public function actionLocations(): void
    {
        $this->template->locations = $this->enumerableSorter->sortByCode($this->currentEntity->getLocations());
    }

    public function actionPlaces(): void
    {
        $this->template->locations = $this->enumerableSorter->sortByCode($this->currentEntity->getLocations());
        $this->template->places = $this->enumerableSorter->sortByCodeArr($this->currentEntity->getPlaces());
    }

    public function actionAcquisitions(): void
    {
        $this->template->acquisitions = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideAcquisitions($this->currentEntity));
        $this->template->disposals = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideDisposals($this->currentEntity));
    }

    public function actionAssetTypes(): void
    {
        $this->template->assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
    }

    public function actionCategories(): void
    {
        $this->template->groups = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getDepreciationGroupsWithoutAccounting()->toArray());
        $this->template->categories = $this->enumerableSorter->sortByCode($this->currentEntity->getCategories());
    }

    public function actionDepreciationGroups(): void
    {
        $cpSelect = $this->getCoeffPercentageForSelect();
        $methodNames = DepreciationMethod::getNames();
        $methodIds = [1,2,3,4];

        $this->template->groups = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getDepreciationGroups()->toArray());
        $this->template->cpSelect = $cpSelect;
        $this->template->methods = $methodIds;
        $this->template->methodNames = $methodNames;
    }

    protected function createComponentAddLocationForm(): Form
    {
        $form = new Form;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $validationMsg = $this->dialsCodeValidator->isLocationValid($this->currentEntity, $values->code);
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }


        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->addLocationAction->__invoke($this->currentEntity, $values->name, $values->code);
            $this->flashMessage('Středisko bylo přidáno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentAddPlaceForm(): Form
    {
        $form = new Form;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;

        $locations = $this->template->locations;
        $locationsIds = $this->getLocationsForSelect($locations);
        $form
            ->addSelect('location', 'Středisko', $locationsIds)
            ->setRequired(true)
        ;

        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->location === 0) {
                $form['location']->addError('Vyberte prosím středisko');
                $this->flashMessage('Vyberte prosím středisko',FlashMessageType::ERROR);
                return;
            }

            $validationMsg = $this->dialsCodeValidator->isPlaceValid($this->currentEntity, $values->code);
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }

        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {

            $this->addPlaceAction->__invoke($values->location, $values->name, $values->code);
            $this->flashMessage('Místo bylo přidáno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentAddAcquisitionForm(): Form
    {
        $form = new Form;
        $form
            ->addText('name', 'Způsob pořízení')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;

        $form
            ->addCheckbox('is_disposal', 'Způsob vyřazení')
        ;

        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->is_disposal) {
                $validationMsg = $this->dialsCodeValidator->isDisposalValid($this->currentEntity, $values->code);
            } else {
                $validationMsg = $this->dialsCodeValidator->isAcquisitionValid($this->currentEntity, $values->code);
            }

            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->addAcquisitionAction->__invoke($this->currentEntity, $values->name, $values->code, $values->is_disposal);
            $this->flashMessage('Záznam byl přidán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditAcquisitionForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Způsob pořízení/vyřazení')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form
            ->addCheckbox('is_disposal', 'Vyřazení')
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->is_disposal) {
                $disposal = $this->disposalRepository->find((int)$values->id);
                if (!$disposal) {
                    $form->addError('Záznam nebyl nalezen.');
                    $this->flashMessage('Záznam nebyl nalezen.', FlashMessageType::ERROR);
                    return;
                }
                $entity = $disposal->getEntity();
                $form = $this->checkAccessToElementsEntity($form, $entity);

                $validationMsg = $this->dialsCodeValidator->isDisposalValid($entity, $values->code, $disposal->getCode());
            } else {
                $acquisition = $this->acquisitionRepository->find((int)$values->id);
                if (!$acquisition) {
                    $form->addError('Záznam nebyl nalezen.');
                    $this->flashMessage('Záznam nebyl nalezen.', FlashMessageType::ERROR);
                    return;
                }
                $entity = $acquisition->getEntity();
                $form = $this->checkAccessToElementsEntity($form, $entity);

                $validationMsg = $this->dialsCodeValidator->isAcquisitionValid($entity, $values->code, $acquisition->getCode());
            }

            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            if ($values->is_disposal) {
                $disposal = $this->disposalRepository->find((int)$values->id);
                $this->editDisposalAction->__invoke($disposal, $values->name, $values->code);

            } else {
                $acquisition = $this->acquisitionRepository->find((int)$values->id);
                $this->editAcquisitionAction->__invoke($acquisition, $values->name, $values->code);
            }
            $this->flashMessage('Záznam byl upraven.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditLocationForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->id);

            if (!$location) {
                $form->addError('Středisko nebylo nalezeno.');
                $this->flashMessage('Středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $location->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            $validationMsg = $this->dialsCodeValidator->isLocationValid($entity, $values->code, $location->getCode());
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->id);
            $this->editLocationAction->__invoke($location, $values->name, $values->code);
            $this->flashMessage('Středisko bylo upraveno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditPlaceForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form
            ->addInteger('location')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->location);
            if (!$location) {
                $form->addError('Vybrané středisko nebylo nalezeno.');
                $this->flashMessage('Vybrané středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $locationEntity = $location->getEntity();

            $place = $this->placeRepository->find((int)$values->id);
            if (!$place) {
                $form->addError('Místo nebylo nalezeno.');
                $this->flashMessage('Místo nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $placeEntity = $place->getLocation()->getEntity();

            if (!$locationEntity || !$placeEntity || !$locationEntity->isEntityUser($this->getCurrentUser()) || !$placeEntity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }

            $validationMsg = $this->dialsCodeValidator->isPlaceValid($placeEntity, $values->code, $place->getCode());
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->location);
            $place = $this->placeRepository->find((int)$values->id);
            $this->editPlaceAction->__invoke($place, $values->name, $values->code, $location);
            $this->flashMessage('Místo bylo upraveno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentDeleteAcquisitionForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $acquisition = $this->acquisitionRepository->find((int)$values->id);

            if (!$acquisition) {
                $form->addError('Záznam nebyl nalezen.');
                $this->flashMessage('Záznam nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $acquisition->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $acquisition = $this->acquisitionRepository->find((int)$values->id);
            $this->deleteAcquisitionAction->__invoke($acquisition);
            $this->flashMessage('Záznam byl smazán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentDeleteLocationForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->id);

            if (!$location) {
                $form->addError('Středisko nebylo nalezeno.');
                $this->flashMessage('Středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $location->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            $places = $location->getPlaces();
            if ($places->count() !== 0) {
                $form->addError('Toto středisko nelze smazat, protože je navázáno na místa v číselníku míst.');
                $this->flashMessage('Toto středisko nelze smazat, protože je navázáno na místa v číselníku míst.',FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find((int)$values->id);
            $this->deleteLocationAction->__invoke($location);
            $this->flashMessage('Středisko bylo smazáno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentDeletePlaceForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $place = $this->placeRepository->find((int)$values->id);
            if (!$place) {
                $form->addError('Místo nebylo nalezeno.');
                $this->flashMessage('Místo nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $place->getLocation()->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $place = $this->placeRepository->find((int)$values->id);
            $this->deletePlaceAction->__invoke($place);
            $this->flashMessage('Místo bylo smazáno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function getLocationsForSelect(array $locations): array
    {
        $locationIds = [];
        $locationIds[0] = 'Vyberte středisko';
        /**
         * @var Location $location
         */
        foreach ($locations as $location) {
            $locationIds[$location->getId()] = $location->getCode() . ' - ' . $location->getName();
        }

        return $locationIds;
    }

    protected function createComponentAddCategoryForm(): Form
    {
        $form = new Form;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $isDepreciableCheckbox = $form
            ->addCheckbox('is_depreciable', '')
            ->setDefaultValue(true)
        ;

        $groupsSelect = $this->getDepreciationGroupsForSelect();
        $form
            ->addSelect('group', 'Odpisová skupina', $groupsSelect)
        ;
        $form
            ->addText('account_asset', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
        ;
        $form
            ->addText('account_depreciation', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->addConditionOn($isDepreciableCheckbox, $form::EQUAL, true)
            ->setRequired(true);
        ;
        $form
            ->addText('account_repairs', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->addConditionOn($isDepreciableCheckbox, $form::EQUAL, true)
            ->setRequired(true);
        ;
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->is_depreciable === true) {
                if ($values->group === 0) {
                    $form['group']->addError('Vyberte prosím odpisovou skupinu');
                    $this->flashMessage('Vyberte prosím odpisovou skupinu',FlashMessageType::ERROR);
                    return;
                }
                $group = $this->depreciationGroupRepository->find($values->group);
                if (!$group) {
                    $form['group']->addError('Zadaná odpisová skupina neexistuje');
                    $this->flashMessage('Zadaná odpisová skupina neexistuje',FlashMessageType::ERROR);
                    return;
                }
            }

            $validationMsg = $this->dialsCodeValidator->isCategoryValid($this->currentEntity, $values->code);
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            if ($values->is_depreciable === true) {
                $group = $this->depreciationGroupRepository->find($values->group);
                $request = new CreateCategoryRequest(
                    $values->code,
                    $values->name,
                    $group,
                    $values->account_asset,
                    $values->account_depreciation,
                    $values->account_repairs,
                    $values->is_depreciable
                );
                $this->addCategoryAction->__invoke($this->currentEntity, $request);
            } else {
                $request = new CreateCategoryRequest(
                    $values->code,
                    $values->name,
                    null,
                    $values->account_asset,
                    null,
                    null,
                    $values->is_depreciable
                );
                $this->addCategoryAction->__invoke($this->currentEntity, $request);
            }

            $this->flashMessage('Kategorie byla přidána.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentDeleteCategoryForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $category = $this->categoryRepository->find((int)$values->id);

            if (!$category) {
                $form->addError('Kategorie nebyla nalezena.');
                $this->flashMessage('Kategorie nebyla nalezena.', FlashMessageType::ERROR);
                return;
            }
            $entity = $category->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $category = $this->categoryRepository->find((int)$values->id);
            $this->deleteCategoryAction->__invoke($category);
            $this->flashMessage('Kategorie byla smazána.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditAssetTypeForm(): Form
    {
        $form = new Form;

        $form
            ->addInteger('id', 'Identifikační číslo')
            ->setRequired(true)
        ;
        $form
            ->addInteger('series', 'Číselná řada')
            ->addRule($form::MAX_LENGTH, 'Maximální délka číselné řady je 8 čísel',8)
            ->addRule($form::MIN, 'Minimální číselná řada je 1', 1)
            ->setRequired(true)
        ;
        $form
            ->addInteger('step', 'Krok')
            ->addRule($form::MAX_LENGTH, 'Maximální délka kroku řady je 8 čísel', 8)
            ->addRule($form::MIN, 'Minimální krok je 1', 1)
            ->setRequired(true)
        ;
        $form->addSubmit('send', 'Uložit');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $assetType = $this->assetTypeRepository->find($values->id);
            if (!$assetType) {
                $form->addError('Druh majetku nebyl nalezen.');
                $this->flashMessage('Způsob pořízení nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $assetType->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

//            if ($values->step * 10 > $values->series) {
//                $form->addError('Krok musí být alespoň 10x menší než číselná řada');
//                return;
//            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $assetType = $this->assetTypeRepository->find($values->id);
            $this->editAssetTypeAction->__invoke($assetType, $values->series, $values->step);
            $this->flashMessage('Způsob pořízení byl upraven.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function getDepreciationGroupsForSelect(): array
    {
        $groups = $this->currentEntity->getDepreciationGroupsWithoutAccounting();

        $groupsArr = [];
        $groupsArr[0] = 'Vyberte odpisovou skupinu';
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            $groupsArr[$group->getId()] = $group->getFullName();
        }

        return $groupsArr;
    }

    protected function createComponentAddDepreciationGroupForm(): Form
    {
        $form = new Form;
        $methodNames = DepreciationMethod::getNamesForSelect();
        $form
            ->addSelect('method', 'Způsob odpis', $methodNames)
            ->setRequired(true)
        ;
        $form
            ->addInteger('group_number', 'Odpis. skupina')
            ->addRule($form::MIN, 'Číslo odpisové skupiny musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Číslo odpisové skupiny může být nejvýše 6', 6)
            ->setNullable()
        ;
        $form
            ->addText('prefix')
            ->addRule($form::PATTERN, 'Prefix může obsahovat jen 1 písmeno.', '[a-zA-Z]?')
        ;
        $form
            ->addInteger('years', 'Počet let')
            ->addRule($form::MIN, 'Počet let musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Počet let může být nejvýše 100', 100)
        ;
        $form
            ->addInteger('months', 'Počet roků')
            ->addRule($form::MIN, 'Počet měsíců musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Počet měsíců může být nejvýše 1000', 1000)
        ;
        $cpSelect = $this->getCoeffPercentageForSelect();
        $form
            ->addSelect('is_coefficient', 'Koef./Procento', $cpSelect)
            ->setRequired(true)
        ;
        $form
            ->addText('rate_first_year', 'Sazba 1. rok')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setNullable()
        ;
        $form
            ->addText('rate', 'Sazba další roky')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setNullable()
        ;
        $form
            ->addText('rate_increased_price', 'Sazba zvýš. VC')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setNullable()
        ;
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->method === 0) {
                $form['method']->addError('Vyberte prosím způsob odpisu');
                $this->flashMessage('Vyberte prosím způsob odpisu',FlashMessageType::ERROR);
                return;
            }

            $validationMsg = $this->dialsCodeValidator->isDeprecationGroupValid($this->currentEntity, $values->group_number, $values->method, $values->prefix);
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }

            if ($values->method !== DepreciationMethod::ACCOUNTING) {
                $this->checkDepreciationTime($form, $values->years, $values->months);

                if (!$values->group_number) {
                    $form['group_number']->addError('Toto pole je povinné.');
                }
                if ($values->rate_first_year === null) {
                    $form['rate_first_year']->addError('Toto pole je povinné.');
                }
                if ($values->rate === null) {
                    $form['rate']->addError('Toto pole je povinné.');
                }
                if ($values->rate_increased_price === null) {
                    $form['rate_increased_price']->addError('Toto pole je povinné.');
                }

            }

            if ($values->years !== null && $values->months !== null) {
                $form->addError('Může být vyplněno pouze jedno z polí "Počet let" a "Počet měsíců".');
                $this->flashMessage('Může být vyplněno pouze jedno z polí "Počet let" a "Počet měsíců".',FlashMessageType::ERROR);
            }

            $isCoefficient = $values->is_coefficient === 1;
            if ($values->method === DepreciationMethod::UNIFORM && $isCoefficient) {
                $form['is_coefficient']->addError('U rovnoměrného způsobu odpisování musí být zvolena možnost "Procento"');
                $this->flashMessage('U rovnoměrného způsobu odpisování musí být zvolena možnost "Procento"',FlashMessageType::ERROR);
            }
            if ($values->method === DepreciationMethod::ACCELERATED && !$isCoefficient) {
                $form['is_coefficient']->addError('U zrychleného způsobu odpisování musí být zvolena možnost "Koeficient"');
                $this->flashMessage('U rovnoměrného způsobu odpisování musí být zvolena možnost "Koeficient"',FlashMessageType::ERROR);
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $isCoefficient = $values->is_coefficient === 1;

            $request = new CreateDepreciationGroupRequest(
                $values->method,
                $values->group_number,
                $values->prefix,
                $values->years,
                $values->months,
                $isCoefficient,
                $values->rate_first_year,
                $values->rate,
                $values->rate_increased_price,
            );
            $this->addDepreciationGroupAction->__invoke($this->currentEntity, $request);
            $this->flashMessage('Odpisová skupina byla přidána.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function checkDepreciationTime(Form $form, $years, $months): void
    {
        if ($years === null && $months === null) {
            $form->getComponent('years')->addError('Musí být zadán buď počet let nebo měsíců');
            $form->getComponent('months')->addError('Musí být zadán buď počet let nebo měsíců');
            $this->flashMessage('Musí být zadán buď počet let nebo měsíců',FlashMessageType::ERROR);
        }
    }

    protected function getCoeffPercentageForSelect(): array
    {
        return [0 => 'Procento', 1 => 'Koeficient', '2' => 'Vlastní způsob'];
    }

    protected function createComponentDeleteDepreciationGroupForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $group = $this->depreciationGroupRepository->find((int)$values->id);

            if (!$group) {
                $form->addError('Odpisová skupina nebyla nalezena.');
                $this->flashMessage('Odpisová skupina nebyla nalezena.', FlashMessageType::ERROR);
                return;
            }
            $entity = $group->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $group = $this->depreciationGroupRepository->find((int)$values->id);
            $this->deleteDepreciationGroupAction->__invoke($group);
            $this->flashMessage('Odpisová skupina byla smazána.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditDepreciationGroupForm(): Form
    {
        $form = new Form;

        $form
            ->addInteger('id', 'id')
            ->setRequired(true)
        ;
        $form
            ->addInteger('method', 'Způsob odpisu')
            ->addRule($form::MIN, 'Vyberte prosím platný způsob odpisu', 1)
            ->addRule($form::MAX, 'Vyberte prosím platný způsob odpisu', 3)
            ->setRequired(true)
        ;
        $form
            ->addInteger('group_number', 'Odpis. skupina')
            ->addRule($form::MIN, 'Musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Může být nejvýše 6', 6)
            ->setRequired(true)
        ;
        $form
            ->addText('prefix')
            ->addRule($form::PATTERN, 'Může obsahovat jen 1 písmeno.', '[a-zA-Z]?')
        ;
        $form
            ->addInteger('years', 'Počet let')
            ->addRule($form::MIN, 'Musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Může být nejvýše 100', 100)
        ;
        $form
            ->addInteger('months', 'Počet měsíců')
            ->addRule($form::MIN, 'Musí být nejméně 1', 1)
            ->addRule($form::MAX, 'Může být nejvýše 999', 999)
        ;
        $form
            ->addInteger('is_coefficient', 'Koef./Procento')
            ->setRequired(true)
        ;
        $form
            ->addText('rate_first_year', 'Sazba 1. rok')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setRequired(true)
        ;

        $form
            ->addText('rate', 'Sazba další roky')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setRequired(true)
        ;
        $form
            ->addText('rate_increased_price', 'Sazba zvýš. VC')
            ->addRule($form::FLOAT, 'Zadejte číslo')
            ->addRule($form::MIN, 'Sazba musí být nejméně 0', 0)
            ->addRule($form::MAX, 'Sazba může být nejvýše 100', 100)
            ->setRequired(true)
        ;
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $group = $this->depreciationGroupRepository->find((int)$values->id);
            if (!$group) {
                $form->addError('Odpisová skupina nebyla nalezena.');
                $this->flashMessage('Odpisová skupina nebyla nalezena.', FlashMessageType::ERROR);
                return;
            }

            $entity = $group->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            $validationMsg = $this->dialsCodeValidator->isDeprecationGroupValid
            (
                $this->currentEntity,
                $values->group_number,
                $values->method,
                $values->prefix,
                $group->getGroup(),
                $group->getMethod(),
                $group->getPrefix()
            );
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }

            if ($values->years === null && $values->months === null) {
                $form->getComponent('years')->addError('Musí být zadán buď počet let nebo měsíců');
                $form->getComponent('months')->addError('Musí být zadán buď počet let nebo měsíců');
                $this->flashMessage('Musí být zadán buď počet let nebo měsíců',FlashMessageType::ERROR);
            }

            if ($values->years !== null && $values->months !== null) {
                $form->addError('Může být vyplněno pouze jedno z polí "Počet let" a "Počet měsíců".');
                $this->flashMessage('Může být vyplněno pouze jedno z polí "Počet let" a "Počet měsíců".',FlashMessageType::ERROR);
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $group = $this->depreciationGroupRepository->find((int)$values->id);
            $isCoefficient = $values->is_coefficient === 1;
            $request = new CreateDepreciationGroupRequest(
                $values->method,
                $values->group_number,
                $values->prefix,
                $values->years,
                $values->months,
                $isCoefficient,
                $values->rate_first_year,
                $values->rate,
                $values->rate_increased_price,
            );
            $this->editDepreciationGroupAction->__invoke($group, $request);
            $this->flashMessage('Odpisová skupina byla upravena.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentEditCategoryForm(): Form
    {
        $form = new Form;

        $form
            ->addInteger('id', 'id')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $isDepreciableCheckbox = $form
            ->addCheckbox('is_depreciable', '')
        ;
        $form
            ->addInteger('group', 'Odpisová skupina')
        ;
        $form
            ->addText('account_asset', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
        ;
        $form
            ->addText('account_depreciation', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->addConditionOn($isDepreciableCheckbox, $form::EQUAL, true)
            ->setRequired(true);
        ;
        $form
            ->addText('account_repairs', 'Název')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
            ->addConditionOn($isDepreciableCheckbox, $form::EQUAL, true)
            ->setRequired(true);
        ;
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $category = $this->categoryRepository->find((int)$values->id);
            if (!$category) {
                $form->addError('Kategorie nebyla nalezena.');
                $this->flashMessage('Kategorie nebyla nalezena.', FlashMessageType::ERROR);
                return;
            }

            $entity = $category->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            if ($values->is_depreciable === true) {
                $group = $this->depreciationGroupRepository->find((int)$values->group);
                if (!$group || $group->getEntity()->getId() !== $this->currentEntityId) {
                    $form->addError('Odpisová skupina nebyla nalezena.');
                    $this->flashMessage('Odpisová skupina nebyla nalezena.', FlashMessageType::ERROR);
                    return;
                }
            }

            $validationMsg = $this->dialsCodeValidator->isCategoryValid($this->currentEntity, $values->code, $category->getCode());
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $category = $this->categoryRepository->find((int)$values->id);

            if ($values->is_depreciable === true) {
                $group = $this->depreciationGroupRepository->find($values->group);
                $request = new CreateCategoryRequest(
                    $values->code,
                    $values->name,
                    $group,
                    $values->account_asset,
                    $values->account_depreciation,
                    $values->account_repairs,
                    $values->is_depreciable
                );
                $this->editCategoryAction->__invoke($category, $request);
            } else {
                $request = new CreateCategoryRequest(
                    $values->code,
                    $values->name,
                    null,
                    $values->account_asset,
                    null,
                    null,
                    $values->is_depreciable
                );
                $this->editCategoryAction->__invoke($category, $request);
            }
            $this->flashMessage('Kategorie byla upravena.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function checkAccessToElementsEntity(Form $form, ?AccountingEntity $entity): Form
    {
        if (!$entity || $entity->getId() !== $this->currentEntityId) {
            $form->addError('K této akci nemáte oprávnění.');
            $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
        }

        return $form;
    }
}