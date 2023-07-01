<?php

namespace App\User\Action;

use App\Entity\User;
use App\User\Exception\EmailIsAlreadyUsedException;
use App\User\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;
use Nette\Utils\Strings;

class RegisterAction
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

    public function __invoke(RegisterRequest $request)
    {
        $checkUser = $this->userRepository->findByEmail($request->email);
        if ($checkUser !== null) {
            throw new EmailIsAlreadyUsedException();
        }
        $request->email = Strings::lower($request->email);
        $request->password = $this->passwords->hash($request->password);
        $user = new User($request->email, $request->password, $request->name, $request->surname, $request->registrationDate);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
