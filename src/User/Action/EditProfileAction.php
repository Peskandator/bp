<?php

namespace App\User\Action;

use App\Entity\User;
use App\User\Exception\EmailIsAlreadyUsedException;
use App\User\Exception\PasswordsNotMatchingException;
use App\User\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;
use Nette\Utils\Strings;

class EditProfileAction
{
    private Passwords $passwords;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        Passwords $passwords,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ) {
        $this->passwords = $passwords;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(RegisterRequest $request, User $editedUser): void
    {
        $request->email = Strings::lower($request->email);
        $checkUser = $this->userRepository->findByEmail($request->email);
        if ($checkUser !== null && $checkUser->getId() !== $editedUser->getId()) {
            throw new EmailIsAlreadyUsedException();
        }

        if ($request->password !== '') {
            if ($request->passwordAgain !== $request->password) {
                throw new PasswordsNotMatchingException();
            }

            $request->password = $this->passwords->hash($request->password);
            $editedUser->setPassword($request->password);
        }
        $editedUser->update($request->email, $request->name, $request->surname);

        $this->entityManager->flush();
    }
}
