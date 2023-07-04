<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\AccountingEntity;
use App\Majetek\Action\CreateEntityAction;
use App\Majetek\Action\CreateEntityRequest;
use App\Majetek\Action\EditEntityAction;
use App\Majetek\ORM\AccountingEntityRepository;
use App\Presenters\BaseAdminPresenter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class EntitiesOverviewPresenter extends BaseAdminPresenter
{
    private AccountingEntityRepository $accountingEntityRepository;
    private CreateEntityAction $createEntityAction;
    private EditEntityAction $editEntityAction;

    public function __construct(
        AccountingEntityRepository $accountingEntityRepository,
        CreateEntityAction $createEntityAction,
        EditEntityAction $editEntityAction
    )
    {
        parent::__construct();
        $this->accountingEntityRepository = $accountingEntityRepository;
        $this->createEntityAction = $createEntityAction;
        $this->editEntityAction = $editEntityAction;
    }

    public function actionDefault(): void
    {
        $entities = $this->accountingEntityRepository->findAll();

        $this->template->entities = $entities;


        $currentEntityId = 0;

        if (isset($this->currentEntityId)) {
            $currentEntityId = $this->currentEntityId;
        }
        $this->template->currentEntityId = $currentEntityId;
    }

    public function actionCreateNew(): void
    {
    }

    public function actionEdit(int $entityId): void
    {
        // TODO validace
        $editedEntity = $this->accountingEntityRepository->find($entityId);
        $this->template->entity = $editedEntity;
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
}