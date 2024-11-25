<?php

namespace App\Presenters;


abstract class BaseAccountingEntityPresenter extends BaseAdminPresenter
{
    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);
        if (isset($this->currentEntityId)) {
            $currentEntity = $this->findAccountingEntityById();
            if ($currentEntity === null) {
                $this->redirect(':Admin:Dashboard:default', ['currentEntityId' => null]);
            }
        } else {
            $this->redirect(':Admin:Dashboard:default', ['currentEntityId' => null]);
        }
    }
}
