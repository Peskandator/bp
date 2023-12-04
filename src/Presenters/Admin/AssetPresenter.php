<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Majetek\Components\AssetFormJsonGenerator;
use App\Majetek\Forms\AssetFormFactory;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Forms\EditAccountingDepreciationFormFactory;
use App\Odpisy\Forms\EditTaxDepreciationFormFactory;
use App\Presenters\BaseAdminPresenter;
use App\Utils\EnumerableSorter;
use Nette\Application\UI\Form;

final class AssetPresenter extends BaseAdminPresenter
{
    private AssetFormFactory $assetFormFactory;
    private EnumerableSorter $enumerableSorter;
    private AssetFormJsonGenerator $jsonGenerator;
    private EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory;
    private EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        AssetFormFactory $assetFormFactory,
        EnumerableSorter $enumerableSorter,
        AssetFormJsonGenerator $jsonGenerator,
        EditTaxDepreciationFormFactory $editTaxDepreciationFormFactory,
        EditAccountingDepreciationFormFactory $editAccountingDepreciationFormFactory,
        EditDepreciationCalculator $editDepreciationCalculator,
    )
    {
        parent::__construct();
        $this->assetFormFactory = $assetFormFactory;
        $this->enumerableSorter = $enumerableSorter;
        $this->jsonGenerator = $jsonGenerator;
        $this->editTaxDepreciationFormFactory = $editTaxDepreciationFormFactory;
        $this->editAccountingDepreciationFormFactory = $editAccountingDepreciationFormFactory;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
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
}
