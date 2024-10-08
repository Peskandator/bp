<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Components\Breadcrumb\BreadcrumbItem;
use App\Odpisy\ORM\DepreciationTaxRepository;
use App\Presenters\BaseAdminPresenter;

final class DashboardPresenter extends BaseAdminPresenter
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
                'Dashboard',
                null)
        );

        if (isset($this->currentEntity) && $this->currentEntity !== null) {
            $this->template->entityId = $this->currentEntityId;
        } else {
            $this->template->entityId = null;
        }
    }
}