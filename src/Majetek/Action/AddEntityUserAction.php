<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\EntityUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AddEntityUserAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, User $user): void
    {
        $entityUser = new EntityUser($user, $entity, false);
        $this->entityManager->persist($entityUser);

        $user->getEntityUsers()->add($entityUser);
        $entity->getEntityUsers()->add($entityUser);

        $this->entityManager->flush();
    }
}
