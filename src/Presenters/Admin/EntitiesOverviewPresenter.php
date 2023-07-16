<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\AccountingEntity;
use App\Entity\EntityUser;
use App\Entity\User;
use App\Majetek\Action\AddEntityUserAction;
use App\Majetek\Action\AppointEntityAdminAction;
use App\Majetek\Action\CreateEntityAction;
use App\Majetek\Requests\CreateEntityRequest;
use App\Majetek\Action\DeleteEntityUserAction;
use App\Majetek\Action\EditEntityAction;
use App\Majetek\ORM\AccountingEntityRepository;
use App\Majetek\ORM\EntityUserRepository;
use App\Presenters\BaseAdminPresenter;
use App\User\ORM\UserRepository;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class EntitiesOverviewPresenter extends BaseAdminPresenter
{
    private AccountingEntityRepository $accountingEntityRepository;
    private CreateEntityAction $createEntityAction;
    private EditEntityAction $editEntityAction;
    private UserRepository $userRepository;
    private AddEntityUserAction $addEntityUserAction;
    private EntityUserRepository $entityUserRepository;
    private DeleteEntityUserAction $deleteEntityUserAction;
    private AppointEntityAdminAction $appointEntityAdminAction;

    public function __construct(
        AccountingEntityRepository $accountingEntityRepository,
        CreateEntityAction $createEntityAction,
        EditEntityAction $editEntityAction,
        UserRepository $userRepository,
        AddEntityUserAction $addEntityUserAction,
        EntityUserRepository $entityUserRepository,
        DeleteEntityUserAction $deleteEntityUserAction,
        AppointEntityAdminAction $appointEntityAdminAction
    )
    {
        parent::__construct();
        $this->accountingEntityRepository = $accountingEntityRepository;
        $this->createEntityAction = $createEntityAction;
        $this->editEntityAction = $editEntityAction;
        $this->userRepository = $userRepository;
        $this->addEntityUserAction = $addEntityUserAction;
        $this->entityUserRepository = $entityUserRepository;
        $this->deleteEntityUserAction = $deleteEntityUserAction;
        $this->appointEntityAdminAction = $appointEntityAdminAction;
    }

    public function actionDefault(): void
    {
        $currentUser = $this->getCurrentUser();
        $this->template->entities = $this->getEntitiesForUser($currentUser);
        $currentEntityId = 0;

        if (isset($this->currentEntityId)) {
            $currentEntityId = $this->currentEntityId;
        }
        $this->template->currentEntityId = $currentEntityId;
        $this->template->signedUser = $currentUser;
    }

    public function actionCreateNew(): void
    {
    }

    public function actionEdit(int $entityId): void
    {
        $this->checkEntityAdmin();

        $editedEntity = $this->accountingEntityRepository->find($entityId);
        $this->template->entity = $editedEntity;
    }

    public function actionManageUsers(int $entityId): void
    {
        $currentUser = $this->getCurrentUser();
        $editedEntity = $this->accountingEntityRepository->find($entityId);
        $this->template->entity = $editedEntity;
        $this->template->entityUsers = $editedEntity->getEntityUsers();
        $this->template->signedUser = $currentUser;
        $this->template->isEntityAdmin = $currentUser->isEntityAdmin($editedEntity);
    }

    protected function createComponentCreateAccountingEntityForm(): Form
    {
        $form = new Form;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
        ;
        $form
            ->addText('company_id', 'IČO')
        ;
        $form
            ->addText('country', 'Stát')
            ->setRequired(true)
        ;
        $form
            ->addText('zip_code', 'PSČ')
            ->setRequired(true)
        ;
        $form
            ->addText('city', 'Město')
            ->setRequired(true)
        ;
        $form
            ->addText('street', 'Ulice')
            ->setRequired(true)
        ;

        $form->addSubmit('send', 'Vytvořit účetní jednotku');

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $request = new CreateEntityRequest(
                $values->name,
                $values->company_id,
                $values->country,
                $values->city,
                $values->zip_code,
                $values->street
            );
            $newEntityId = $this->createEntityAction->__invoke($request);
            $this->flashMessage('Účetní jednotka byla vytvořena', FlashMessageType::SUCCESS);
            $this->redirect(':Admin:EntitiesOverview:default', ['currentEntityId' => $newEntityId]);
        };

        return $form;
    }

    protected function createComponentEditAccountingEntityForm(): Form
    {
        /**
         * @var AccountingEntity $entity
         */
        $entity = $this->template->entity;

        $form = new Form;
        $form
            ->addText('name', 'Název')
            ->setRequired(true)
            ->setDefaultValue($entity->getName())
        ;
        $form
            ->addText('company_id', 'IČO')
            ->setDefaultValue($entity->getCompanyId())
        ;
        $form
            ->addText('country', 'Stát')
            ->setRequired(true)
            ->setDefaultValue($entity->getCountry())
        ;
        $form
            ->addText('zip_code', 'PSČ')
            ->setRequired(true)
            ->setDefaultValue($entity->getZipCode())
        ;
        $form
            ->addText('city', 'Město')
            ->setRequired(true)
            ->setDefaultValue($entity->getCity())
        ;
        $form
            ->addText('street', 'Ulice')
            ->setRequired(true)
            ->setDefaultValue($entity->getStreet())
        ;
        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($entity) {
            $request = new CreateEntityRequest(
                $values->name,
                $values->company_id,
                $values->country,
                $values->city,
                $values->zip_code,
                $values->street
            );
            $this->editEntityAction->__invoke($request, $entity);
            $this->flashMessage('Účetní jednotka byla upravena', FlashMessageType::SUCCESS);
            $this->redirect(':Admin:EntitiesOverview:default');
        };

        return $form;
    }

    protected function createComponentAddEntityUserForm(): Form
    {
        $isEntityAdmin = $this->template->isEntityAdmin;

        /**
         * @var AccountingEntity $editedEntity
         */
        $editedEntity = $this->template->entity;

        $form = new Form;
        $form
            ->addEmail('email', 'E-mailová adresa')
            ->setRequired(true)
        ;
        $form->addSubmit('send', 'Přidat uživatele');

        $form->onValidate[] = function (Form $form, \stdClass $values) use($editedEntity, $isEntityAdmin) {
            $usersWithAccess = $editedEntity->getEntityUsers();
            $addingUser = $this->userRepository->findByEmail($values->email);

            if (!$isEntityAdmin) {
                $this->addNoPermissionError();
            }

            if ($addingUser === null) {
                $form->addError('Uživatel se zadanou e-mailovou adresou neni zaregistrován.');
                $this->flashMessage('Uživatel se zadanou e-mailovou adresou neni zaregistrován.', FlashMessageType::ERROR);
                return;
            }

            /**
             * @var EntityUser $userWithAccess
             */
            foreach ($usersWithAccess as $userWithAccess) {
                if ($userWithAccess->getUser()->getEmail() === $values->email) {
                    $form->addError('Uživatel má již přístup k této účetní jednotce.');
                    $this->flashMessage('Uživatel má již přístup k této účetní jednotce.', FlashMessageType::ERROR);
                    return;
                }
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use($editedEntity) {

            $addingUser = $this->userRepository->findByEmail($values->email);
            if ($addingUser === null) {
                return;
            }

            $this->addEntityUserAction->__invoke($editedEntity, $addingUser);

            $this->flashMessage('Uživateli byl přidán přístup.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentDeleteEntityUserForm(): Form
    {
        $isEntityAdmin = $this->template->isEntityAdmin;

        $form = new Form;
        $form->addHidden('entity_user_id')
            ->setRequired(true);
        $form
            ->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($isEntityAdmin) {
            $entityUser = $this->entityUserRepository->find($values->entity_user_id);

            if (!$isEntityAdmin) {
                $this->addNoPermissionError();
            }

            if (!$entityUser) {
                $form->addError('Uživatel již nemá k této akci přístup.');
                $this->flashMessage('Uživatel již nemá k této akci přístup.', FlashMessageType::ERROR);
                return;
            }

            if ($entityUser->getUser()->getId() === $this->getCurrentUser()->getId()) {
                $form->addError('Uživatele nelze smazat.');
                $this->flashMessage('Uživatele nelze smazat.', FlashMessageType::ERROR);
                return;
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $entityUser = $this->entityUserRepository->find($values->entity_user_id);
            if ($entityUser === null) {
                return;
            }
            $this->deleteEntityUserAction->__invoke($entityUser);
            $this->flashMessage('Uživateli byl odebrán přístup.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentAppointEntityAdminForm(): Form
    {
        $isEntityAdmin = $this->template->isEntityAdmin;

        $form = new Form;
        $form->addHidden('entity_user_id')
            ->setRequired(true);
        $form
            ->addSubmit('send');

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($isEntityAdmin) {
            $entityUser = $this->entityUserRepository->find($values->entity_user_id);

            if (!$isEntityAdmin) {
                $this->addNoPermissionError();
            }

            if (!$entityUser) {
                $form->addError('Uživatel již nemá k této akci přístup.');
                $this->flashMessage('Uživatel již nemá k této akci přístup.', FlashMessageType::ERROR);
                return;
            }

            if ($entityUser->getUser()->getId() === $this->getCurrentUser()->getId()) {
                $form->addError('Uživatele nelze jmenovat administrátorem.');
                $this->flashMessage('Uživatele nelze jmenovat administrátorem.', FlashMessageType::ERROR);
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $currentUser = $this->getCurrentUser();
            $entityUser = $this->entityUserRepository->find($values->entity_user_id);
            if ($entityUser === null) {
                return;
            }
            $this->appointEntityAdminAction->__invoke($entityUser, $currentUser);
            $this->flashMessage('Uživateli byl odebrán přístup.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }

    protected function getEntitiesForUser(User $user): array
    {
        $entities = [];
        $entityUsers = $user->getEntityUsers();

        /**
         * @var EntityUser $entityUser
         */
        foreach ($entityUsers as $entityUser) {
            $entities[] = $entityUser->getEntity();
        }

        return $entities;
    }
}