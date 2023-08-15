<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Asset;
use App\Entity\AssetType;
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

    public function __construct(
        AssetFormFactory $assetFormFactory,
        AcquisitionsProvider $acquisitionsProvider,
        EnumerableSorter $enumerableSorter,
    )
    {
        parent::__construct();
        $this->assetFormFactory = $assetFormFactory;
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->enumerableSorter = $enumerableSorter;
    }

    public function actionDefault(int $assetId): void
    {
        $asset = $this->findAssetById($assetId);
        $this->template->asset = $asset;
        $this->template->depreciationGroupsTax = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getDepreciationGroupsWithoutAccounting()->toArray());
        $this->template->depreciationGroupsAccounting = $this->enumerableSorter->sortGroupsByMethodAndNumber($this->currentEntity->getAccountingDepreciationGroups()->toArray());
        $this->template->categories = $this->enumerableSorter->sortByCode($this->currentEntity->getCategories());
        $this->template->acquisitions = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideAcquisitions($this->currentEntity));
        $this->template->locations = $this->enumerableSorter->sortByCode($this->currentEntity->getLocations());
        $this->template->places = $this->enumerableSorter->sortByCodeArr($this->currentEntity->getPlaces());
        $this->template->disposals = $this->enumerableSorter->sortByCodeArr($this->acquisitionsProvider->provideDisposals($this->currentEntity));
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->assetTypes = $assetTypes;
        $this->template->nextInventoryNumbers = $this->getNextNumberForAssetTypes($assetTypes);
    }

    protected function createComponentEditAssetForm(): Form
    {
        $asset = $this->template->asset;
        $form = $this->assetFormFactory->create($this->currentEntity, true, $asset);
        $this->assetFormFactory->fillInForm($form, $asset);
        return $form;
    }

    protected function getNextNumberForAssetTypes(array $assetTypes): array
    {

        $nextNumbers = [];

        /**
         * @var AssetType $assetType
         */
        foreach ($assetTypes as $assetType) {
            $series = $assetType->getSeries();
            $step = $assetType->getStep();
            $numberFound = false;

            $counter = 0;

            while ($numberFound === false) {
                $newSeriesNumber = $series + $step * $counter;
                if (!$this->isInventoryNumberAvailable($newSeriesNumber)) {
                    $counter++;
                    continue;
                }
                $nextNumbers[$assetType->getId()] = $newSeriesNumber;
                $numberFound = true;
            }

        }

        return $nextNumbers;
    }

    protected function isInventoryNumberAvailable(int $number): bool
    {
        $assets = $this->currentEntity->getAssets();
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
