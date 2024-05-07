<?php

namespace App\Presenters;

use App\Components\AdminMenu\AdminMenu;
use App\Components\AdminMenu\AdminMenuFactoryInterface;
use App\Components\Breadcrumb\Breadcrumb;
use App\Components\Breadcrumb\BreadcrumbFactoryInterface;
use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Entity\User;
use App\Majetek\ORM\AccountingEntityRepository;
use App\Majetek\ORM\AssetRepository;
use App\Utils\CurrentUser;
use App\Utils\FlashMessageType;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

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
