<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Reports\Components\AssetHTMLGenerator;
use App\Reports\Components\AssetReportsFilter;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Reports\Enums\AssetColumns;
use App\Reports\Forms\FilterAssetsForReportFormFactory;
use Dompdf\Dompdf;
use Nette\Application\UI\Form;

final class AssetReportsPresenter extends BaseAccountingEntityPresenter
{
    private FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory;
    private AssetReportsFilter $assetReportsFilter;
    private AssetHTMLGenerator $assetHTMLGenerator;

    public function __construct(
        FilterAssetsForReportFormFactory $filterAssetsForReportFormFactory,
        AssetReportsFilter $assetReportsFilter,
        AssetHTMLGenerator $assetHTMLGenerator,
    )
    {
        parent::__construct();
        $this->filterAssetsForReportFormFactory = $filterAssetsForReportFormFactory;
        $this->assetReportsFilter = $assetReportsFilter;
        $this->assetHTMLGenerator = $assetHTMLGenerator;
    }

    public function actionDefault(?string $filter = null): void
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
        $this->template->filter = $filter;
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
        $groupedBy = $filterData['grouping'] !== 'none' ? AssetColumns::NAMES[$filterData['grouping']] : null;
        $columns = $this->assetReportsFilter->getColumnNamesFromFilter($filterData);
        $firstRow = $this->assetReportsFilter->getFirstRowColumns($filterData);
        $summedColumns = $filterData['summing'];
        $this->template->entity = $this->currentEntity;
        $this->template->assetsGrouped = $records;
        $this->template->columns = $columns;
        $this->template->firstRow = $firstRow;
        $this->template->summedColumns = $summedColumns;
        $this->template->groupedBy = $groupedBy;
        $this->template->exportFilter = $filter;
    }

    public function actionExport(string $filter)
    {
        $htmlData = $this->assetHTMLGenerator->generate($this->currentEntity, $filter);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlData);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($this->currentEntity->getName() . ' - Sestava majetku');
    }

    protected function createComponentFilterAssetsForReportForm(): Form
    {
        $form = $this->filterAssetsForReportFormFactory->create($this->currentEntity);

        $filter = $this->template->filter ?? null;
        if ($filter !== null) {
            $filterDataStdClass = json_decode(urldecode($filter));
            $filterData = json_decode(json_encode($filterDataStdClass), true);
            $this->filterAssetsForReportFormFactory->setDefaultsFromFilter($form, $filterData);
        }

        return $form;
    }
}