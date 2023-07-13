<?php

namespace App\Presenters;

use App\Entity\AccountingEntity;
use App\Entity\User;
use App\Majetek\ORM\AccountingEntityRepository;
use App\Utils\CurrentUser;
use App\Utils\FlashMessageType;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;

abstract class BaseAdminPresenter extends Presenter
{
    #[Persistent]
    public int $currentEntityId;
    private CurrentUser $currentUser;
    private AccountingEntityRepository $entityRepository;
    public AccountingEntity $currentEntity;


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

    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect(':Home:default');
        }
        $currentUser = $this->getCurrentUser();
        if (isset($this->currentEntityId)) {
            $currentEntity = $this->entityRepository->find($this->currentEntityId);

            if ($currentEntity === null) {
                $this->redirect(':Admin:Dashboard:default', ['currentEntityId' => null]);
            }
            if (!$currentUser->isEntityUser($currentEntity)) {
                $this->addNoPermissionError(true);
            }

            $this->currentEntity = $currentEntity;
        }
    }

    public function getCurrentUser(): User
    {
        $loggedInUser = $this->currentUser->getCurrentLoggedInUser();
        if ($loggedInUser === null) {
            $this->redirect(':Home:default');
        }
        return $loggedInUser;
    }

    public function addNoPermissionError(bool $unsetEntity = false): void
    {
        $this->flashMessage(
            'K této akci nemáte oprávnění',
            FlashMessageType::ERROR
        );

        if ($unsetEntity) {
            $this->redirect(':Admin:Dashboard:default', ['currentEntityId' => null]);
        }

        $this->redirect(':Admin:Dashboard:default');
    }

    public function checkEntityAdmin(): void
    {
        $currentEntity = $this->entityRepository->find($this->currentEntityId);

        if (!$currentEntity) {
            $this->addNoPermissionError();
        }

        $currentUser = $this->getCurrentUser();
        if (!$currentUser->isEntityAdmin($currentEntity)) {
            $this->addNoPermissionError();
        }
    }
}
