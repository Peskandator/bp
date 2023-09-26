<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Asset;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Presenters\BaseAdminPresenter;
use App\Utils\AcquisitionsProvider;
use App\Utils\EnumerableSorter;
use Nette\Application\UI\Form;

final class AssetsPresenter extends BaseAdminPresenter
{
    private AcquisitionsProvider $acquisitionsProvider;
    private EnumerableSorter $enumerableSorter;
    private AssetFormFactory $assetFormFactory;
    private AssetFormJsonGenerator $jsonGenerator;

    public function __construct(
        AcquisitionsProvider $acquisitionsProvider,
        EnumerableSorter $enumerableSorter,
        AssetFormFactory $assetFormFactory,
        AssetFormJsonGenerator $jsonGenerator
    )
    {
        parent::__construct();
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->enumerableSorter = $enumerableSorter;
        $this->assetFormFactory = $assetFormFactory;
        $this->jsonGenerator = $jsonGenerator;
    }

    public function actionDefault(?int $view = null): void
    {
        $assets = $this->getFilteredAssets($view);;
        $this->template->assets = $assets;
        $this->template->activeTab = $view;
    }

    public function actionCreate(): void
    {
        $this->template->depreciationGroupsTax = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getDepreciationGroupsWithoutAccounting()->toArray());
        $this->template->depreciationGroupsAccounting = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getAccountingDepreciationGroups()->toArray());
        $this->template->categories = $this->enumerableSorter->sortByCode($this->currentEntity->getCategories());
        $this->template->acquisitions = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideAcquisitions($this->currentEntity));
        $this->template->places = $this->enumerableSorter->sortByCodeArr($this->currentEntity->getPlaces());
        $this->template->disposals = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideDisposals($this->currentEntity));
        $this->template->categoriesGroupsJson = $this->jsonGenerator->createCategoriesGroupsJson($this->currentEntity);
        $this->template->placesLocationsJson = $this->jsonGenerator->createPlacesLocationsJson($this->currentEntity);
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->assetTypes = $assetTypes;
        $this->template->nextInventoryNumbers = $this->jsonGenerator->getNextNumberForAssetTypesJson($this->currentEntity, $assetTypes);
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
}