<?php

namespace App\Majetek\Action;

use App\Entity\Disposal;
use Doctrine\ORM\EntityManagerInterface;

class DeleteDisposalAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Disposal $disposal): void
    {
        $this->entityManager->remove($disposal);
        $this->entityManager->flush();
    }
}
