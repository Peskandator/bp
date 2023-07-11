<?php

declare(strict_types=1);

namespace App\Presenters\Admin;
use App\Entity\User;
use App\Presenters\BaseAdminPresenter;
use App\User\Action\EditProfileAction;
use App\User\Action\RegisterRequest;
use App\User\Exception\EmailIsAlreadyUsedException;
use App\User\Exception\PasswordsNotMatchingException;
use App\Utils\FlashMessageType;
use Nette\Application\UI\Form;

final class ProfilePresenter extends BaseAdminPresenter
{
    private EditProfileAction $editProfileAction;

    public function __construct(
        EditProfileAction $editProfileAction
    )
    {
        parent::__construct();
        $this->editProfileAction = $editProfileAction;
    }

    public function actionDefault(): void
    {
        $this->template->signedUser = $this->getCurrentUser();
    }

    protected function createComponentEditUserProfileForm(): Form
    {
        /**
         * @var User $editedUser
         */
        $editedUser = $this->template->signedUser;

        $form = new Form;
        $form
            ->addText('new_first_name', 'Jméno')
            ->setRequired(true)
            ->setDefaultValue($editedUser->getFirstName())
        ;
        $form
            ->addText('new_last_name', 'Příjmení')
            ->setRequired(true)
            ->setDefaultValue($editedUser->getLastName())
        ;
        $form
            ->addEmail('new_email', 'E-mailová adresa')
            ->setRequired(true)
            ->setDefaultValue($editedUser->getEmail())
        ;

        $form
            ->addPassword('new_password', 'Nové heslo')
            ->addRule($form::MIN_LENGTH, 'Heslo musí obsahovat alespoň 7 znaků.', 7)
        ;
        $form
            ->addPassword('new_password_again', 'Potrzení hesla')
        ;

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($editedUser) {
            $request = new RegisterRequest(
                $values->new_first_name,
                $values->new_last_name,
                $values->new_email,
                $values->new_password,
                $values->new_password_again,
                $editedUser->getRegistrationDate()
            );
            try {
                $this->editProfileAction->__invoke($request, $editedUser);
            } catch (EmailIsAlreadyUsedException $e) {
                $form->getComponent('new_email')->addError('Účet se zadanou e-mailovou adresou již existuje');
                $this->flashMessage('Účet se zadanou e-mailovou adresou již existuje', FlashMessageType::ERROR);
                return;
            } catch (PasswordsNotMatchingException $e) {
                $form->getComponent('new_password')->addError('Zadaná hesla se musí shodovat.');
                $this->flashMessage('Zadaná hesla se musí shodovat.', FlashMessageType::ERROR);
                return;
            }


            $this->flashMessage('Profil byl úspěšně změněn.', FlashMessageType::SUCCESS);
            $this->redirect('this');
        };

        return $form;
    }



}