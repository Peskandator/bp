<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Odpisy\ORM\DepreciationTaxRepository;
use App\Presenters\BaseAdminPresenter;

final class AssetReportsPresenter extends BaseAdminPresenter
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
                'Sestavy majetku',
                null)
        );
    }
}