<?php

namespace App\Majetek\Action;

use App\Entity\EntityUser;
use Doctrine\ORM\EntityManagerInterface;

class DeleteEntityUserAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(EntityUser $entityUser): void
    {
        $this->entityManager->remove($entityUser);
        $this->entityManager->flush();
    }
}
