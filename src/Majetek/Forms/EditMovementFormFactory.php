<?php

namespace App\Majetek\Forms;

use App\Entity\AccountingEntity;
use App\Entity\Movement;
use App\Majetek\Action\EditMovementAction;
use App\Majetek\Enums\MovementType;
use App\Majetek\ORM\MovementRepository;
use App\Utils\DateTimeFormatter;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

class EditMovementFormFactory
{
    private MovementRepository $movementRepository;
    private EditMovementAction $editMovementAction;
    private DateTimeFormatter $dateTimeFormatter;

    public function __construct(
        MovementRepository $movementRepository,
        EditMovementAction $editMovementAction,
        DateTimeFormatter $dateTimeFormatter,
    )
    {
        $this->movementRepository = $movementRepository;
        $this->editMovementAction = $editMovementAction;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function create(AccountingEntity $currentEntity): Form
    {
        $form = new Form;

        $form
            ->addInteger('id', 'id')
            ->setRequired(true)
        ;
        $form
            ->addText('description', 'Popis')
            ->setMaxLength(256)
        ;
        $form
            ->addText('date', 'Datum')
            ->setNullable()
        ;
        $form
            ->addText('acc_debited', 'Účet MD')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
        ;
        $form
            ->addText('acc_credited', 'Účet DAL')
            ->addRule($form::LENGTH, 'Délka účtu musí být 6 znaků.', 6)
        ;
        $form
            ->addCheckbox('accountable', 'Účet DAL')
        ;

        $form->onValidate[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $movement = $this->movementRepository->find($values->id);
            if ($movement === null) {
                $form->addError('Pohyb nebyl nalezen.');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'Pohyb nebyl nalezen.');
                return;
            }

            $entity = $movement->getEntity();
            if ($entity->getId() !== $currentEntity->getId()) {
                $form->addError('K této akci nemáte oprávnění.');
                $form->getPresenter()->flashMessage(FlashMessageType::ERROR, 'K této akci nemáte oprávnění.');
            }

            $movementType = $movement->getType();
            $date = $this->dateTimeFormatter->changeToDateFormat($values->date);
            $asset = $movement->getAsset();
            $assetEntryDate = $asset->getEntryDate();
            $assetDisposalDate = $asset->getDisposalDate();

            if ($movementType !== MovementType::INCLUSION && $date< $assetEntryDate) {
                $errMsg = 'Datum pohybu nemůže být dříve než datum zařazení';
                $form['date']->addError($errMsg);
                $form->getPresenter()->flashMessage($errMsg);
            }

            if ($movementType !== MovementType::DISPOSAL && $assetDisposalDate && $date > $assetDisposalDate) {
                $errMsg = 'Datum pohybu nemůže být později než datum vyřazení';
                $form['date']->addError($errMsg);
                $form->getPresenter()->flashMessage($errMsg);
            }

            if ($movementType === MovementType::INCLUSION) {
                $allMovements = $asset->getMovements();
                $conflict = false;
                /**
                 * @var Movement $movementItem
                 */
                foreach ($allMovements as $movementItem) {
                    if ($movementItem->getId() === $movement->getId()) {
                        continue;
                    }
                    if ($movementItem->getDate() < $date) {
                        $conflict = true;
                    }
                }
                if ($conflict) {
                    $errMsg = 'Datum pohybu zařazení nemůže být později než data ostatních pohybů';
                    $form['date']->addError($errMsg);
                    $form->getPresenter()->flashMessage($errMsg);
                }
            }

            if ($movementType === MovementType::DISPOSAL) {
                $allMovements = $asset->getMovements();
                $conflict = false;
                /**
                 * @var Movement $movementItem
                 */
                foreach ($allMovements as $movementItem) {
                    if ($movementItem->getId() === $movement->getId()) {
                        continue;
                    }
                    if ($movementItem->getDate() > $date) {
                        $conflict = true;
                    }
                }
                if ($conflict) {
                    $errMsg = 'Datum pohybu vyřazení nemůže být dříve než data ostatních pohybů';
                    $form['date']->addError($errMsg);
                    $form->getPresenter()->flashMessage($errMsg);
                }
            }
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($currentEntity) {
            $movement = $this->movementRepository->find($values->id);
            $values->date = $this->dateTimeFormatter->changeToDateFormat($values->date);
            $this->editMovementAction->__invoke($movement, $values->description, $values->date, $values->acc_debited, $values->acc_credited, $values->accountable);
            $form->getPresenter()->flashMessage('Pohyb byl úspěšně upraven.', FlashMessageType::SUCCESS);
            $form->getPresenter()->redirect('this');
        };

        return $form;
    }
}