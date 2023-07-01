<?php

declare(strict_types=1);

namespace App\Presenters;

use App\User\Action\RegisterAction;
use App\User\Action\RegisterRequest;
use App\User\Exception\EmailIsAlreadyUsedException;
use Nette\Application\UI\Form;


final class RegistrationPresenter extends BasePresenter
{
    private RegisterAction $registerAction;

    public function __construct(
        RegisterAction $registerAction,
    )
    {
        $this->registerAction = $registerAction;
        parent::__construct();
    }

    public function actionDefault(): void
    {
    }

    protected function createComponentRegistrationForm(): Form
    {
        $form = new Form;
        $form
            ->addText('name', 'Jméno')
            ->setRequired(true)
        ;
        $form
            ->addText('surname', 'Příjmení')
            ->setRequired(true)
        ;
        $form
            ->addEmail('email', 'E-mailová adresa')
            ->setRequired(true)
        ;

        $form
            ->addPassword('password', 'Heslo')
            ->addRule($form::MIN_LENGTH, 'Heslo musí obsahovat minimálně 7 znaků.', 7)
            ->setRequired(true)
        ;

        $form->addSubmit('send', 'Registrovat');
        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $request = new RegisterRequest(
                $values->name,
                $values->surname,
                $values->email,
                $values->password,
                new \DateTimeImmutable(),
            );

            try {
                $this->registerAction->__invoke($request);
            } catch (EmailIsAlreadyUsedException $e) {
                $form->getComponent('email')->addError('Účet se zadaným jménem byl již vytvořen');
                return;
            }
            $this->flashMessage('Byl jste úspěšně registrován.');
            $this->redirect('this');
        };

        return $form;
    }
}