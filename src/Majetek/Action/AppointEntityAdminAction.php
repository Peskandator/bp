<?php

namespace App\Majetek\Action;

use App\Entity\EntityUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AppointEntityAdminAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(EntityUser $entityUser, User $currentUser): void
    {
        $entity = $entityUser->getEntity();
        $currentEntityUser = $currentUser->getEntityUser($entity);

        $entityUser->setIsEntityAdmin(true);
        $currentEntityUser->setIsEntityAdmin(false);

        $this->entityManager->flush();
    }
}
