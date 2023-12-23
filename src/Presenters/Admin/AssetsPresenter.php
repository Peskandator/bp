<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Asset;
use App\Majetek\Action\DeleteAssetAction;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Majetek\ORM\AssetRepository;
use App\Presenters\BaseAdminPresenter;
use App\Utils\EnumerableSorter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class AssetsPresenter extends BaseAdminPresenter
{
    private EnumerableSorter $enumerableSorter;
    private AssetFormFactory $assetFormFactory;
    private AssetFormJsonGenerator $jsonGenerator;
    private AssetRepository $assetRepository;
    private DeleteAssetAction $deleteAssetAction;

    public function __construct(
        EnumerableSorter $enumerableSorter,
        AssetFormFactory $assetFormFactory,
        AssetFormJsonGenerator $jsonGenerator,
        AssetRepository $assetRepository,
        DeleteAssetAction $deleteAssetAction,
    )
    {
        parent::__construct();
        $this->enumerableSorter = $enumerableSorter;
        $this->assetFormFactory = $assetFormFactory;
        $this->jsonGenerator = $jsonGenerator;
        $this->assetRepository = $assetRepository;
        $this->deleteAssetAction = $deleteAssetAction;
    }

    public function actionDefault(?int $view = null): void
    {
        $assets = $this->getFilteredAssets($view);
        $this->template->assets = $assets;
        $this->template->activeTab = $view;
    }

    public function actionCreate(): void
    {
        $this->template->categoriesGroupsJson = $this->jsonGenerator->createCategoriesGroupsJson($this->currentEntity);
        $this->template->placesLocationsJson = $this->jsonGenerator->createPlacesLocationsJson($this->currentEntity);
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->nextInventoryNumbers = $this->jsonGenerator->getNextNumberForAssetTypesJson($this->currentEntity, $assetTypes, null);
        $this->template->assetTypeCodes = $this->jsonGenerator->getAssetTypeCodesJson($this->currentEntity, $assetTypes);
        $this->template->acquisitionCodes = $this->jsonGenerator->getAcquisitionCodesJson($this->currentEntity);
        $this->template->groupsInfoJson = $this->jsonGenerator->getGroupsInfoJson($this->currentEntity);
    }

    protected function createComponentCreateAssetForm(): Form
    {
        $form = $this->assetFormFactory->create($this->currentEntity, false);
        return $form;
    }

    protected function getFilteredAssets(?int $view): array
    {
        $assets = $this->currentEntity->getAssetsSorted();
        $filteredAssets = [];

        if ($view !== null && $view < 5 && $view > 0) {
            /**
             * @var Asset $asset
             */
            foreach ($assets as $asset) {
                if ($asset->getAssetType()->getCode() === $view) {
                    $filteredAssets[] = $asset;
                }
            }
            return $filteredAssets;
        }

        return $assets;
    }

    protected function createComponentDeleteAssetForm(): Form
    {
        $form = new Form;

        $form
            ->addHidden('id')
            ->setRequired(true)
        ;
        $form->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $asset = $this->assetRepository->find((int)$values->id);

            if (!$asset) {
                $form->addError('Majetek nebyl nalezen.');
                $this->flashMessage('Majetek nebyl nalezen.', FlashMessageType::ERROR);
                return;
            }
            $entity = $asset->getEntity();
            $form = $this->checkAccessToElementsEntity($form, $entity);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $asset = $this->assetRepository->find((int)$values->id);
            $this->deleteAssetAction->__invoke($asset);
            $this->flashMessage('Majetek byl smazán.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }
}