<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Odpisy\ORM\DepreciationTaxRepository;
use App\Presenters\BaseAccountingEntityPresenter;

final class DepreciationReportsPresenter extends BaseAccountingEntityPresenter
{
    public function __construct(
        DepreciationTaxRepository $depreciationTaxRepository,
    )
    {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Sestavy odpisů',
                null)
        );
    }
}