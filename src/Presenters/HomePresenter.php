<?php

declare(strict_types=1);

namespace App\Presenters;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\User;

final class HomePresenter extends BasePresenter
{
    private User $userManager;
    private Authenticator $authenticator;

    public function __construct(
        User $userManager,
        Authenticator $authenticator
    )
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->authenticator = $authenticator;
    }

    public function actionDefault(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect(':Admin:Dashboard:default');
        }
        $this->getSession()->start();
    }

    public function actionSignOut(): void
    {
        if ($this->session->isStarted()) {
            $this->session->destroy();
        }
        $this->getUser()->logout(true);
        $this->flashMessage('Byl jste odhlášen.');
        $this->redirect(':Home:default');
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form
            ->addText('email', 'E-mailová adresa')
            ->setRequired(true)
        ;
        $form
            ->addPassword('password', 'Heslo')
            ->setRequired(true)
        ;
        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            try {
                $identity = $this->authenticator->authenticate($values->email, $values->password);
                $this->userManager->login($identity);
            } catch (AuthenticationException $e) {
                $errorType = $e->getMessage();

                if ($errorType === 'incorrect_password') {
                    $form->addError('Zadali jste nesprávné heslo.');
                    return;
                }
                $form->addError('Účet se zadanou e-mailovou adresou neexistuje.');
                return;
            }

            $this->flashMessage('Přihlášení proběhlo úspěšně.');
            $this->redirect('this');
        };

        return $form;
    }
}