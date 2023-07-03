<?php

namespace App\Presenters;

use App\Majetek\ORM\AccountingEntityRepository;
use App\Utils\CurrentUser;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;

abstract class BaseAdminPresenter extends Presenter
{
    #[Persistent]
    public int $currentEntityId;
    private CurrentUser $currentUser;
    private AccountingEntityRepository $entityRepository;


    public function injectBaseDeps(
        AccountingEntityRepository $entityRepository
    ) {
        $this->entityRepository = $entityRepository;
    }

    public function beforeRender()
    {
        $currentEntity = null;

        if (isset($this->currentEntityId)) {
            $currentEntity = $this->entityRepository->find($this->currentEntityId);
        }
        $this->template->currentEntity = $currentEntity;
    }


    public function injectCurrentUser(
        CurrentUser $currentUser
    )
    {
        $this->currentUser = $currentUser;
    }
}
