<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Presenters\BaseAdminPresenter;
use App\Utils\AcquisitionsProvider;
use App\Utils\EnumerableSorter;
use Nette\Application\UI\Form;

final class AssetPresenter extends BaseAdminPresenter
{
    private AssetFormFactory $assetFormFactory;
    private AcquisitionsProvider $acquisitionsProvider;
    private EnumerableSorter $enumerableSorter;
    private AssetFormJsonGenerator $jsonGenerator;

    public function __construct(
        AssetFormFactory $assetFormFactory,
        AcquisitionsProvider $acquisitionsProvider,
        EnumerableSorter $enumerableSorter,
        AssetFormJsonGenerator $jsonGenerator
    )
    {
        parent::__construct();
        $this->assetFormFactory = $assetFormFactory;
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->enumerableSorter = $enumerableSorter;
        $this->jsonGenerator = $jsonGenerator;
    }

    public function actionDefault(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->template->asset = $asset;
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

        $this->template->activeTab = 1;
    }

    public function actionMovements(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->template->asset = $asset;
        $this->template->activeTab = 2;
    }

    public function actionDepreciations(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);

        $this->template->asset = $asset;
        $this->template->taxDepreciations = $asset->getTaxDepreciations();
        $this->template->accountingDepreciations = $asset->getAccountingDepreciations();
        $this->template->activeTab = 3;
    }

    protected function createComponentEditAssetForm(): Form
    {
        $asset = $this->template->asset;
        $form = $this->assetFormFactory->create($this->currentEntity, true, $asset);
        $this->assetFormFactory->fillInForm($form, $asset);
        return $form;
    }
}
