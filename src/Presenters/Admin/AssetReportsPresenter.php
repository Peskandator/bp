<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Entity\Asset;
use App\Majetek\ORM\AssetRepository;
use App\Presenters\BaseAdminPresenter;
use App\Reports\Forms\FilterAssetsForReportFormFactory;
use App\Utils\DateTimeFormatter;
use Nette\Application\UI\Form;

final class AssetReportsPresenter extends BaseAdminPresenter
{
    private FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory;
    private AssetRepository $assetRepository;
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(
        FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory,
        AssetRepository $assetRepository,
        DateTimeFormatter $dateTimeFormatter,
    )
    {
        parent::__construct();
        $this->filterAssetsForReportFormFactory = $filterAssetsForReportFormFactory;
        $this->assetRepository = $assetRepository;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function actionDefault(): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy majetku',
                null)
        );

        $this->template->entity = $this->currentEntity;
    }

    public function actionResult(string $filter): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy majetku',
                $this->lazyLink('AssetReports:default'))
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Výsledek',
                null)
        );

        $filterDataStdClass = json_decode(urldecode($filter));

        $filterData =  json_decode(json_encode($filterDataStdClass), true);
        $filteredAssets = $this->getFilteredResults($filterData);

        $this->template->assets = $filteredAssets;
        $this->template->entity = $this->currentEntity;
    }

    protected function createComponentFilterAssetsForReportForm(): Form
    {
        $form = $this->filterAssetsForReportFormFactory->create($this->currentEntity);
        return $form;
    }

    protected function getFilteredResults(array $filter): array
    {
        $assets = $this->assetRepository->findAll();
        $result = [];

        $allowedTypes = $filter['entry_price_from'];
        $allowedCategories = $filter['entry_price_from'];
        $allowedPlaces = $filter['entry_price_from'];
        $fromPrice = $filter['entry_price_from'];
        $toPrice = $filter['entry_price_to'];
        $withDisposed = $filter['disposed'];

        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            if ($this->currentEntity->getId() !== $asset->getEntity()->getId()) {
                continue;
            }

            if ($asset->isDisposed() && !$withDisposed) {
                continue;
            }

            if ($allowedTypes && count($allowedTypes) > 0 && !in_array($asset->getAssetType()->getId(), $allowedTypes)) {
                continue;
            }

            if ($allowedCategories && count($allowedCategories) > 0 && !in_array($asset->getCategory()->getId(), $allowedCategories)) {
                continue;
            }

            if ($allowedPlaces && count($allowedPlaces) > 0 && !in_array($asset->getPlace()->getId(), $allowedPlaces)) {
                continue;
            }

            $fromDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($fromDate && $asset->getEntryDate() < $fromDate) {
                continue;
            }
            $toDate = $this->dateTimeFormatter->changeToDateFormat($filter['from_date']);
            if ($toDate && $asset->getEntryDate() > $toDate) {
                continue;
            }

            $currentYear = (int)date("Y");
            if ($fromPrice !== null && $asset->getPriceForYear($currentYear) < $fromPrice) {
                continue;
            }
            if ($toPrice !== null && $asset->getPriceForYear($currentYear) > $toPrice) {
                continue;
            }

            $result[] = $asset;
        }

        return $result;
    }
}