<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Presenters\BaseAdminPresenter;

final class AccountingFromMovementsPresenter extends BaseAdminPresenter
{

    public function __construct(
    )
    {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Zaúčtování',
                null)
        );
        $this->getComponent('breadcrumb')->addItem(
            new BreadcrumbItem(
                'Z pohybů',
                null)
        );
    }
}