<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Odpisy\Action\EditAccountingDepreciationAction;
use App\Odpisy\Action\EditTaxDepreciationAction;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\ORM\DepreciationAccountingRepository;
use App\Odpisy\ORM\DepreciationTaxRepository;
use App\Odpisy\Requests\EditDepreciationRequest;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class EditDepreciationFormFactory
{
    private DepreciationTaxRepository $depreciationTaxRepository;
    private DepreciationAccountingRepository $depreciationAccountingRepository;
    private EditTaxDepreciationAction $editTaxDepreciationAction;
    private EditAccountingDepreciationAction $editAccountingDepreciationAction;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        DepreciationTaxRepository $depreciationTaxRepository,
        DepreciationAccountingRepository $depreciationAccountingRepository,
        EditTaxDepreciationAction $editTaxDepreciationAction,
        EditAccountingDepreciationAction $editAccountingDepreciationAction,
        EditDepreciationCalculator $editDepreciationCalculator,
    )
    {
        $this->depreciationTaxRepository = $depreciationTaxRepository;
        $this->depreciationAccountingRepository = $depreciationAccountingRepository;
        $this->editTaxDepreciationAction = $editTaxDepreciationAction;
        $this->editAccountingDepreciationAction = $editAccountingDepreciationAction;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function create(AccountingEntity $currentEntity): Form
    {
        $form = new Form;

        $form
            ->addInteger('id', 'id')
            ->setRequired(true)
        ;
        $form
            ->addText('amount', 'Vstupní cena')
            ->addRule($form::FLOAT, 'Zadejte platné číslo')
            ->addRule($form::MIN, 'Prosím zadejte číslo větší než 0', 0)
            ->setRequired()
        ;
        $form
            ->addText('percentage', 'Procento')
            ->addRule($form::FLOAT, 'Zadejte platné číslo')
            ->addRule($form::MIN, 'Prosím zadejte číslo větší než 0', 0)
            ->addRule($form::MAX, 'Prosím zadejte číslo menší nebo rovno 100', 100)
            ->setRequired()
        ;
        $form
            ->addCheckbox('executable', '')
        ;
        $form
            ->addCheckbox('is_accounting', '')
        ;

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            if ($values->is_accounting) {
                $depreciation = $this->depreciationAccountingRepository->find($values->id);
            } else {
                $depreciation = $this->depreciationTaxRepository->find($values->id);
            }
            if ($depreciation === null) {
                $form->addError('Odpis nebyl nalezen.');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'Odpis nebyl nalezen.');
                return;
            }

            $entity = $depreciation->getEntity();
            if ($entity->getId() !== $currentEntity->getId()) {
                $form->addError('K této akci nemáte oprávnění.');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'K této akci nemáte oprávnění.');
            }

            if ($values->executable && !$this->editDepreciationCalculator->isEditedDepreciationAmountValid($depreciation, $values->amount)) {
                $form['amount']->addError('Odpis je příliš vysoký. Oprávky by byly vyšší než vstupní cena');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'Odpis je příliš vysoký. Oprávky by byly vyšší než vstupní cena');
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $isAccounting = $values->is_accounting;
            if ($isAccounting) {
                $depreciation = $this->depreciationAccountingRepository->find($values->id);
            } else {
                $depreciation = $this->depreciationTaxRepository->find($values->id);
            }

            $request = new EditDepreciationRequest(
                $values->id,
                $values->amount,
                $values->percentage,
                $values->executable,
            );

            if ($isAccounting) {
                $this->editAccountingDepreciationAction->__invoke($depreciation, $request);
            } else {
                $this->editTaxDepreciationAction->__invoke($depreciation, $request);
            }
            $form->getPresenter()->flashMessage('Odpis byl úspěšně upraven. Neprovedené odpisy následujících let byly přepočítány.', FlashMessageType::SUCCESS);
        };

        return $form;
    }

}