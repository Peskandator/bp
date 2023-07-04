<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use Doctrine\ORM\EntityManagerInterface;

class EditEntityAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,

    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateEntityRequest $request, AccountingEntity $entity): void
    {
        $entity->update($request);
        $this->entityManager->flush();
    }
}
