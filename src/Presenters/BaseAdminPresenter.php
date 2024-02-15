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

abstract class BaseAdminPresenter extends Presenter
{
    #[Persistent]
    public int $currentEntityId;
    private CurrentUser $currentUser;
    private AccountingEntityRepository $entityRepository;
    public AccountingEntity $currentEntity;
    private AdminMenuFactoryInterface $adminMenuFactory;
    private BreadcrumbFactoryInterface $breadcrumbFactory;
    private AssetRepository $assetRepository;


    public function injectBaseDeps(
        AccountingEntityRepository $entityRepository,
        AssetRepository $assetRepository
    ) {
        $this->entityRepository = $entityRepository;
        $this->assetRepository = $assetRepository;
    }

    public function injectAdminMenuFactory(
        AdminMenuFactoryInterface $adminMenuFactory,
        BreadcrumbFactoryInterface $breadcrumbFactory
    )
    {
        $this->adminMenuFactory = $adminMenuFactory;
        $this->breadcrumbFactory = $breadcrumbFactory;
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

    protected function createComponentAdminMenu(): AdminMenu
    {
        return $this->adminMenuFactory->create();
    }

    protected function createComponentBreadcrumb(): Breadcrumb
    {
        return $this->breadcrumbFactory->create();
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

    public function findAssetById(int $assetId): Asset
    {
        if (!$this->currentEntity) {
            $this->addNoPermissionError();
        }

        $asset = $this->assetRepository->find($assetId);

        if (!$asset) {
            $this->flashMessage(
                'Majetek neexistuje',
                FlashMessageType::ERROR
            );
            $this->redirect(':Admin:Assets:default');
        }

        if ($asset->getEntity()->getId() !== $this->currentEntity->getId()) {
            $this->addNoPermissionError();
        }

        return $asset;
    }

    protected function checkAccessToElementsEntity(Form $form, ?AccountingEntity $entity): Form
    {
        if (!$entity || $entity->getId() !== $this->currentEntityId) {
            $form->addError('K této akci nemáte oprávnění.');
            $this->flashMessage('K této akci nemáte oprávnění',FlashMessageType::ERROR);
        }

        return $form;
    }
}
