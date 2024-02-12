<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Asset;
use App\Entity\Movement;
use App\Majetek\Action\DeleteMovementAction;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Majetek\Forms\EditMovementFormFactory;
use App\Majetek\ORM\MovementRepository;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAdminPresenter;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class AssetPresenter extends BaseAdminPresenter
{
    private AssetFormFactory $assetFormFactory;
    private EnumerableSorter $enumerableSorter;
    private AssetFormJsonGenerator $jsonGenerator;
    private EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory;
    private EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory;
    private EditDepreciationCalculator $editDepreciationCalculator;
    private DeleteMovementAction $deleteMovementAction;
    private MovementRepository $movementRepository;
    private EditMovementFormFactory $editMovementFormFactory;

    public function __construct(
        AssetFormFactory $assetFormFactory,
        EnumerableSorter $enumerableSorter,
        AssetFormJsonGenerator $jsonGenerator,
        EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory,
        EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory,
        EditMovementFormFactory $editMovementFormFactory,
        EditDepreciationCalculator $editDepreciationCalculator,
        DeleteMovementAction $deleteMovementAction,
        MovementRepository $movementRepository,
    )
    {
        parent::__construct();
        $this->assetFormFactory = $assetFormFactory;
        $this->enumerableSorter = $enumerableSorter;
        $this->jsonGenerator = $jsonGenerator;
        $this->editTaxDepreciationFormFactory = $editTaxDepreciationFormFactory;
        $this->editAccountingDepreciationFormFactory = $editAccountingDepreciationFormFactory;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
        $this->deleteMovementAction = $deleteMovementAction;
        $this->movementRepository = $movementRepository;
        $this->editMovementFormFactory = $editMovementFormFactory;
    }

    public function actionDefault(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->template->asset = $asset;
        $this->template->categoriesGroupsJson = $this->jsonGenerator->createCategoriesGroupsJson($this->currentEntity);
        $this->template->placesLocationsJson = $this->jsonGenerator->createPlacesLocationsJson($this->currentEntity);
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->nextInventoryNumbers = $this->jsonGenerator->getNextNumberForAssetTypesJson($this->currentEntity, $assetTypes, $asset);
        $this->template->assetTypeCodes = $this->jsonGenerator->getAssetTypeCodesJson($this->currentEntity, $assetTypes);
        $this->template->acquisitionCodes = $this->jsonGenerator->getAcquisitionCodesJson($this->currentEntity);
        $this->template->groupsInfoJson = $this->jsonGenerator->getGroupsInfoJson($this->currentEntity);

        $this->template->activeTab = 1;
    }

    public function actionMovements(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->template->asset = $asset;
        $this->template->activeTab = 2;
        $this->template->movements = $this->getSortedMovements($asset);

    }

    public function actionDepreciations(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);

        $this->template->asset = $asset;
        $this->template->taxDepreciations = $asset->getTaxDepreciations();
        $this->template->accountingDepreciations = $asset->getAccountingDepreciations();
        $this->template->editDepreciationCalculator = $this->editDepreciationCalculator;

        $this->template->activeTab = 3;
    }

    protected function createComponentEditAssetForm(): Form
    {
        $asset = $this->template->asset;
        $form = $this->assetFormFactory->create($this->currentEntity, true, $asset);
        $this->assetFormFactory->fillInForm($form, $asset);
        return $form;
    }

    protected function createComponentEditTaxDepreciationForm(): Form
    {
        $form = $this->editTaxDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentEditAccountingDepreciationForm(): Form
    {
        $form = $this->editAccountingDepreciationFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentEditMovementForm(): Form
    {
        $form = $this->editMovementFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function createComponentDeleteMovementForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $movement = $this->movementRepository->find((int)$values->id);
            if (!$movement) {
                $form->addError('Pohyb nebyl nalezen.');
                $this->flashMessage('Pohyb nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $movement->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);

            if (!$movement->isDeletable()) {
                $errMsg = 'Pohyb nelze smazat kvůli již provedeným odpisům.';
                $form->addError($errMsg);
                $this->flashMessage($errMsg, FlashMessageType::ERROR);
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $movement = $this->movementRepository->find((int)$values->id);
            $this->deleteMovementAction->__invoke($movement);
            $this->flashMessage('Pohyb byl smazán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function getSortedMovements(Asset $asset): array
    {
        $movements = $asset->getMovements()->toArray();

        usort($movements, function(Movement $a, Movement $b) {
            if ($a->getDate() > $b->getDate()) {
                return 1;
            }
            if ($a->getDate() < $b->getDate()) {
                return -1;
            }
            return 0;
        });

        return $movements;
    }
}
