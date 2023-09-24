<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
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


    }
}