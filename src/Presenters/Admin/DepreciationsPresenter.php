<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Presenters\BaseAdminPresenter;

final class DepreciationsPresenter extends BaseAdminPresenter
{

    public function __construct(
    )
    {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        //TODO: možná nějak posortovat
        $this->template->depreciationsTax = $this->currentEntity->getTaxDepreciations();
        $this->template->depreciationsAccounting = $this->currentEntity->getAccountingDepreciations();
    }
}