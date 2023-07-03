<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\EntityUser;
use App\Utils\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;

class CreateEntityAction
{
    private EntityManagerInterface $entityManager;
    private CurrentUser $currentUser;

    public function __construct(
        EntityManagerInterface $entityManager,
        CurrentUser $currentUser
    ) {
        $this->entityManager = $entityManager;
        $this->currentUser = $currentUser;
    }

    public function __invoke(CreateEntityRequest $request)
    {
        $user = $this->currentUser->getCurrentLoggedInUser();

        $entity = new AccountingEntity($request);
        $this->entityManager->persist($entity);

        $entityUser = new EntityUser($user, $entity);
        $this->entityManager->persist($entityUser);

        $user->getEntityUsers()->add($entityUser);
        $entity->getEntityUsers()->add($entityUser);

        $this->entityManager->flush();
    }
}
