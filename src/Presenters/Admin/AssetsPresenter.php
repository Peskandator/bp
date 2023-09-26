<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\Acquisition;
use App\Entity\Asset;
use App\Entity\AssetType;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\Place;
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

    public function __construct(
        AcquisitionsProvider $acquisitionsProvider,
        EnumerableSorter $enumerableSorter,
        AssetFormFactory $assetFormFactory
    )
    {
        parent::__construct();
        $this->acquisitionsProvider = $acquisitionsProvider;
        $this->enumerableSorter = $enumerableSorter;
        $this->assetFormFactory = $assetFormFactory;
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

        $this->template->categoriesGroupsJson = $this->createCategoriesGroupsJson();
        $this->template->placesLocationsJson = $this->createPlacesLocationsJson();
        $assetTypes = $this->enumerableSorter->sortByCode($this->currentEntity->getAssetTypes());
        $this->template->assetTypes = $assetTypes;
        $this->template->nextInventoryNumbers = $this->getNextNumberForAssetTypesJson($assetTypes);
        $this->template->assetTypeCodes = $this->getAssetTypeCodesJson($assetTypes);
        $this->template->acquisitionCodes = $this->getAcquisitionCodesJson();

        $this->template->groupsInfoJson = $this->getGroupsInfoJson();
    }

    protected function createCategoriesGroupsJson(): string
    {
        $jsonArr = [];
        $categories = $this->currentEntity->getCategories();

        /**
         * @var Category $category
         */
        foreach ($categories as $category) {
            $group =  $category->getDepreciationGroup();
            if ($group) {
                $jsonArr[(string)$category->getId()] = $group->getId();
                continue;
            }
            $jsonArr[$category->getId()] = '';
        }

        return json_encode($jsonArr);
    }

    protected function createPlacesLocationsJson(): string
    {
        $jsonArr = [];
        $places = $this->currentEntity->getPlaces();

        /**
         * @var Place $place
         */
        foreach ($places as $place) {
            $locationId = $place->getLocation()->getId();
            $jsonArr[(string)$place->getId()] = $locationId;
        }

        return json_encode($jsonArr);
    }

    protected function createComponentCreateAssetForm(): Form
    {
        $form = $this->assetFormFactory->create($this->currentEntity, false);
        return $form;
    }

    protected function getNextNumberForAssetTypesJson(array $assetTypes): string
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
                $nextNumbers[(string)$assetType->getId()] = $newSeriesNumber;
                $numberFound = true;
            }
        }

        return json_encode($nextNumbers);
    }

    protected function getAssetTypeCodesJson(array $assetTypes): string
    {
        $assetTypeCodes = [];
        /**
         * @var AssetType $assetType
         */
        foreach ($assetTypes as $assetType) {
            $assetTypeCodes[(string)$assetType->getId()] = $assetType->getCode();
        }

        return json_encode($assetTypeCodes);
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

    private function getAcquisitionCodesJson(): string
    {
        $codes = [];
        $acquisitions = $this->currentEntity->getAcquisitions();

        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            $codes[(string)$acquisition->getId()] = $acquisition->getCode();
        }

        return json_encode($codes);
    }

    private function getGroupsInfoJson(): string
    {
        $info = [];
        $groups = $this->currentEntity->getDepreciationGroups();
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            $info[(string)$group->getId()]['rate-first'] = $group->getRateFirstYear();
            $info[(string)$group->getId()]['rate'] = $group->getRate();
            $info[(string)$group->getId()]['rate-increased'] = $group->getRateIncreasedPrice();
            $info[(string)$group->getId()]['years'] = $group->getYears();
            $info[(string)$group->getId()]['months'] = $group->getMonths();
            $info[(string)$group->getId()]['coeff'] = $group->isCoefficient() ? 1 : 0;
        }

        return json_encode($info);
    }
}