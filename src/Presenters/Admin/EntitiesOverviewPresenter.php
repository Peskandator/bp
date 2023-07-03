<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Majetek\Action\CreateEntityAction;
use App\Majetek\Action\CreateEntityRequest;
use App\Majetek\ORM\AccountingEntityRepository;
use App\Presenters\BaseAdminPresenter;
use Nette\Application\UI\Form;

final class EntitiesOverviewPresenter extends BaseAdminPresenter
{
    private AccountingEntityRepository $accountingEntityRepository;
    private CreateEntityAction $createEntityAction;

    public function __construct(
        AccountingEntityRepository $accountingEntityRepository,
        CreateEntityAction $createEntityAction
    )
    {
        parent::__construct();
        $this->accountingEntityRepository = $accountingEntityRepository;
        $this->createEntityAction = $createEntityAction;
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

            $this->createEntityAction->__invoke($request);

            $this->flashMessage('Účetní jednotka byla vytvořena');
            $this->redirect(':Admin:EntitiesOverview:default');
        };

        return $form;
    }
}