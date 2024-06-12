<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Presenters\BaseAccountingEntityPresenter;
use App\Reports\Components\DepreciationReportsFilter;
use App\Reports\Enums\DepreciationColumns;
use App\Reports\Forms\FilterDepreciationsForReportFormFactory;
use Nette\Application\UI\Form;

final class DepreciationReportsPresenter extends BaseAccountingEntityPresenter
{
    private FilterDepreciationsForReportFormFactory $filterDepreciationsForReportFormFactory;
    private DepreciationReportsFilter $depreciationReportsFilter;

    public function __construct(
        FilterDepreciationsForReportFormFactory $filterDepreciationsForReportFormFactory,
        DepreciationReportsFilter $depreciationReportsFilter,
    )
    {
        parent::__construct();
        $this->filterDepreciationsForReportFormFactory = $filterDepreciationsForReportFormFactory;
        $this->depreciationReportsFilter = $depreciationReportsFilter;
    }

    public function actionDefault(): void
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
        $this->template->entity = $this->currentEntity;
        $this->template->depreciationsGrouped = $records;
        $this->template->columns = $this->depreciationReportsFilter->getColumnNamesFromFilter($filterData);
        $this->template->summedColumns = $filterData['summing'];
        $this->template->groupedBy = $groupedBy;
    }

    protected function createComponentFilterDepreciationsForReportForm(): Form
    {
        $form = $this->filterDepreciationsForReportFormFactory->create($this->currentEntity);
        return $form;
    }
}