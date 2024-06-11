<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Majetek\Components\AssetReportsFilter;
use App\Majetek\Enums\AssetColumns;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Reports\Forms\FilterAssetsForReportFormFactory;
use Nette\Application\UI\Form;

final class AssetReportsPresenter extends BaseAccountingEntityPresenter
{
    private FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory;
    private AssetReportsFilter $assetReportsFilter;

    public function __construct(
        FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory,
        AssetReportsFilter $assetReportsFilter,
    )
    {
        parent::__construct();
        $this->filterAssetsForReportFormFactory = $filterAssetsForReportFormFactory;
        $this->assetReportsFilter = $assetReportsFilter;
    }

    public function actionDefault(): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy majetku',
                null)
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Filtr',
                null)
        );

        $this->template->entity = $this->currentEntity;
    }

    public function actionResult(string $filter): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy majetku',
                null
        ));
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Filtr',
                $this->lazyLink('AssetReports:default'))
        );

        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Výsledek',
                null)
        );

        $filterDataStdClass = json_decode(urldecode($filter));
        $filterData = json_decode(json_encode($filterDataStdClass), true);
        $records = $this->assetReportsFilter->getResults($this->currentEntity, $filterData);
        $this->template->entity = $this->currentEntity;
        $this->template->assetsGrouped = $records;
        $this->template->columns = $this->assetReportsFilter->getColumnNamesFromFilter($filterData);
        $this->template->firstRow = $this->assetReportsFilter->getFirstRowColumns($filterData);
    }

    protected function createComponentFilterAssetsForReportForm(): Form
    {
        $form = $this->filterAssetsForReportFormFactory->create($this->currentEntity);
        return $form;
    }
}