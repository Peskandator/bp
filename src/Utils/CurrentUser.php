<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\User;
use App\User\ORM\UserRepository;

class CurrentUser
{
    private \Nette\Security\User $userManager;
    private UserRepository $userRepository;

    public function __construct(
        \Nette\Security\User $userManager,
        UserRepository $userRepository
    ) {
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
    }

    public function getCurrentLoggedInUser(): ?User
    {
        $identity = $this->userManager->getIdentity();
        if ($identity === null) {
            return null;
        }
        $userId = $identity->getId();
        $user = $this->userRepository->find($userId);

        return $user;
    }
}
