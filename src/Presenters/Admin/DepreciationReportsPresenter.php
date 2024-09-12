<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Reports\Components\DepreciationHTMLGenerator;
use App\Reports\Components\DepreciationReportsFilter;
use App\Reports\Enums\DepreciationColumns;
use App\Reports\Forms\FilterDepreciationsForReportFormFactory;
use Dompdf\Dompdf;
use Nette\Application\UI\Form;

final class DepreciationReportsPresenter extends BaseAccountingEntityPresenter
{
    private FilterDepreciationsForReportFormFactory $filterDepreciationsForReportFormFactory;
    private DepreciationReportsFilter $depreciationReportsFilter;
    private DepreciationHTMLGenerator $depreciationHTMLGenerator;

    public function __construct(
        FilterDepreciationsForReportFormFactory $filterDepreciationsForReportFormFactory,
        DepreciationReportsFilter $depreciationReportsFilter,
        DepreciationHTMLGenerator $depreciationHTMLGenerator,
    )
    {
        parent::__construct();
        $this->filterDepreciationsForReportFormFactory = $filterDepreciationsForReportFormFactory;
        $this->depreciationReportsFilter = $depreciationReportsFilter;
        $this->depreciationHTMLGenerator = $depreciationHTMLGenerator;
    }

    public function actionDefault(?string $filter = null): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy odpisů',
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
                'Sestavy odpisů',
                null
            ));
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Filtr',
                $this->lazyLink('DepreciationReports:default'))
        );

        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Výsledek',
                null)
        );

        $filterDataStdClass = json_decode(urldecode($filter));
        $filterData = json_decode(json_encode($filterDataStdClass), true);
        $records = $this->depreciationReportsFilter->getResults($this->currentEntity, $filterData);
        $groupedBy = $filterData['grouping'] !== 'none' ? DepreciationColumns::NAMES[$filterData['grouping']] : null;
        $summedColumns = $filterData['summing'];
        $columns = $this->depreciationReportsFilter->getColumnNamesFromFilter($filterData);
        $this->template->entity = $this->currentEntity;
        $this->template->depreciationsGrouped = $records;
        $this->template->columns = $columns;
        $this->template->summedColumns = $summedColumns;
        $this->template->groupedBy = $groupedBy;
        $this->template->exportFilter = $filter;
    }

    public function actionExport(string $filter)
    {
        $htmlData = $this->depreciationHTMLGenerator->generate($this->currentEntity, $filter);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlData);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($this->currentEntity->getName() . ' - Sestava odpisů');
    }

    protected function createComponentFilterDepreciationsForReportForm(): Form
    {
        $form = $this->filterDepreciationsForReportFormFactory->create($this->currentEntity);

        $filter = $this->template->filter ?? null;
        if ($filter !== null) {
            $filterDataStdClass = json_decode(urldecode($filter));
            $filterData = json_decode(json_encode($filterDataStdClass), true);
            $this->filterDepreciationsForReportFormFactory->setDefaultsFromFilter($form, $filterData);
        }

        return $form;
    }
}