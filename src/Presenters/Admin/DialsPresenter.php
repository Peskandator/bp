<?php

declare(strict_types=1);

namespace App\Presenters\Admin;

use App\Entity\Location;
use App\Majetek\Action\AddAcquisitionAction;
use App\Majetek\Action\AddLocationAction;
use App\Majetek\Action\AddPlaceAction;
use App\Majetek\Action\DeleteAcquisitionAction;
use App\Majetek\Action\DeleteLocationAction;
use App\Majetek\Action\DeletePlaceAction;
use App\Majetek\Action\EditAcquisitionAction;
use App\Majetek\Action\EditLocationAction;
use App\Majetek\Action\EditPlaceAction;
use App\Majetek\ORM\AcquisitionRepository;
use App\Majetek\ORM\LocationRepository;
use App\Majetek\ORM\PlaceRepository;
use App\Presenters\BaseAdminPresenter;
use App\Utils\AcquisitionsProvider;
use App\Utils\DialsCodeValidator;
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
        DeleteLocationAction $deleteLocationAction
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
    }

    public function actionLocations(): void
    {
        $this->template->locations = $this->sortByCode($this->currentEntity->getLocations()->toArray());
    }

    public function actionPlaces(): void
    {
        $this->template->locations = $this->sortByCode($this->currentEntity->getLocations()->toArray());
        $this->template->places = $this->sortByCode($this->currentEntity->getPlaces());
    }

    public function actionAcquisitions(): void
    {
        $defaultAcquisitionsIds = $this->acquisitionsProvider->provideDefaultAcquisitionsIds();
        $this->template->defaultAcquisitionsIds = $defaultAcquisitionsIds;
        $this->template->acquisitions = $this->sortByCode($this->acquisitionsProvider->provideAcquisitions($this->currentEntity));
    }

    public function actionAssetTypes(): void
    {
        $this->template->assetTypes = $this->currentEntity->getAssetTypes();
    }

    public function actionCategories(): void
    {
        $this->template->categories = $this->currentEntity->getCategories();
    }

    public function actionDepreciationGroups(): void
    {
        $this->template->groups = $this->currentEntity->getDepreciationGroups();
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
        $form->addSubmit('send', 'Přidat');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $validationMsg = $this->dialsCodeValidator->isAcquisitionValid($this->currentEntity, $values->code);
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->addAcquisitionAction->__invoke($this->currentEntity, $values->name, $values->code);
            $this->flashMessage('Způsob pořízení byl přidán.', FlashMessageType::SUCCESS);
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
            ->addText('name', 'Způsob pořízení')
            ->setRequired(true)
        ;
        $form
            ->addInteger('code', 'Kód')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $acquisition = $this->acquisitionRepository->find($values->id);

            if (!$acquisition) {
                $form->addError('Způsob pořízení nebyl nalezen.');
                $this->flashMessage('Způsob pořízení nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $acquisition->getEntity();

            if (!$entity || !$entity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }

            $validationMsg = $this->dialsCodeValidator->isAcquisitionValid($entity, $values->code, $acquisition->getCode());
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $acquisition = $this->acquisitionRepository->find($values->id);
            $this->editAcquisitionAction->__invoke($acquisition, $values->name, $values->code);
            $this->flashMessage('Způsob pořízení byl upraven.', FlashMessageType::SUCCESS);
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
            $location = $this->locationRepository->find($values->id);

            if (!$location) {
                $form->addError('Středisko nebylo nalezeno.');
                $this->flashMessage('Středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $location->getEntity();

            if (!$entity || !$entity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }

            $validationMsg = $this->dialsCodeValidator->isLocationValid($entity, $values->code, $location->getCode());
            if ($validationMsg !== '') {
                $form->addError($validationMsg);
                $this->flashMessage($validationMsg,FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find($values->id);
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
            ->addHidden('location')
            ->setRequired(true)
        ;
        $form->addSubmit('send',);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find($values->location);
            if (!$location) {
                $form->addError('Vybrané středisko nebylo nalezeno.');
                $this->flashMessage('Vybrané středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $locationEntity = $location->getEntity();

            $place = $this->placeRepository->find($values->id);
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
            $location = $this->locationRepository->find($values->location);
            $place = $this->placeRepository->find($values->id);
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
            $acquisition = $this->acquisitionRepository->find($values->id);

            if (!$acquisition) {
                $form->addError('Způsob pořízení nebyl nalezen.');
                $this->flashMessage('Způsob pořízení nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $acquisition->getEntity();

            if (!$entity || !$entity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $acquisition = $this->acquisitionRepository->find($values->id);
            $this->deleteAcquisitionAction->__invoke($acquisition);
            $this->flashMessage('Způsob pořízení byl smazán.', FlashMessageType::SUCCESS);
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
            $location = $this->locationRepository->find($values->id);

            if (!$location) {
                $form->addError('Středisko nebylo nalezeno.');
                $this->flashMessage('Středisko nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $location->getEntity();

            if (!$entity || !$entity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }

            $places = $location->getPlaces();
            if ($places->count() !== 0) {
                $form->addError('Toto středisko nelze smazat, protože je navázáno na místa v číselníku míst.');
                $this->flashMessage('Toto středisko nelze smazat, protože je navázáno na místa v číselníku míst.',FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $location = $this->locationRepository->find($values->id);
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
            $place = $this->placeRepository->find($values->id);
            if (!$place) {
                $form->addError('Místo nebylo nalezeno.');
                $this->flashMessage('Místo nebylo nalezeno.', FlashMessageType::ERROR);
                return;
            }
            $entity = $place->getLocation()->getEntity();

            if (!$entity || !$entity->isEntityUser($this->getCurrentUser())) {
                $form->addError('K této akci nemáte oprávnění.');
                $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $place = $this->placeRepository->find($values->id);
            $this->deletePlaceAction->__invoke($place);
            $this->flashMessage('Místo bylo smazáno.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function getLocationsForSelect(array $locations): array
    {
        $locationIds = [];
        /**
         * @var Location $location
         */
        foreach ($locations as $location) {
            $locationIds[$location->getId()] = $location->getId() . ' - ' . $location->getName();
        }

        return $locationIds;
    }

    protected function sortByCode(array $records): array
    {
        usort($records, function ($first, $second) {
            if ($first->getCode() > $second->getCode()) {
                return 1;
            }
            if ($first->getCode() > $second->getCode()) {
                return -1;
            };
            return 0;
        });

        return $records;
    }
}