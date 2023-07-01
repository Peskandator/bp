<?php

namespace App\User;

use App\User\ORM\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class Authenticator implements \Nette\Security\Authenticator
{
    private UserRepository $userRepository;
    private Passwords $passwords;

    public function __construct(
        UserRepository $userRepository,
        Passwords $passwords
    ) {
        $this->userRepository = $userRepository;
        $this->passwords = $passwords;
    }

    public function authenticate(string $email, string $password): IIdentity
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new AuthenticationException('user_not_found');
        }

        $verifyPassword = function (string $password, string $storedPassword) {
            return $this->passwords->verify($password, $storedPassword);
        };
        if (!$user->authenticate($password, $verifyPassword)) {
            throw new AuthenticationException('incorrect_password');
        }

        return new SimpleIdentity(
            $user->getId()
        );
    }
}
