<?php

namespace App\Odpisy\Forms;

use App\Entity\AccountingEntity;
use App\Odpisy\Action\EditAccountingDepreciationAction;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\ORM\DepreciationAccountingRepository;
use App\Odpisy\Requests\EditDepreciationRequest;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class EditAccountingDepreciationFormFactory
{
    private DepreciationAccountingRepository $depreciationAccountingRepository;
    private EditAccountingDepreciationAction $editAccountingDepreciationAction;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        DepreciationAccountingRepository $depreciationAccountingRepository,
        EditAccountingDepreciationAction $editAccountingDepreciationAction,
        EditDepreciationCalculator $editDepreciationCalculator,
    )
    {
        $this->depreciationAccountingRepository = $depreciationAccountingRepository;
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
            ->setRequired()
        ;
        $form
            ->addCheckbox('executable', '')
        ;

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $depreciation = $this->depreciationAccountingRepository->find($values->id);
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
            if ($depreciation->isExecuted()) {
                $form->addError('Nelze upravit již provedený odpis. Nejdříve musíte zrušit provedení.');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'Nelze upravit již provedený odpis. Nejdříve musíte zrušit provedení.');
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $depreciation = $this->depreciationAccountingRepository->find($values->id);
            $request = new EditDepreciationRequest(
                $values->id,
                $values->amount,
                $values->percentage,
                $values->executable,
            );
            $this->editAccountingDepreciationAction->__invoke($depreciation, $request);
            $form->getPresenter()->flashMessage('Odpis byl úspěšně upraven. Neprovedené odpisy následujících let byly přepočítány.', FlashMessageType::SUCCESS);
        };

        return $form;
    }
}