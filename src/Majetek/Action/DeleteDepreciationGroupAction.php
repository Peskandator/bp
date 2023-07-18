<?php

namespace App\Majetek\Action;

use App\Entity\Acquisition;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use Doctrine\ORM\EntityManagerInterface;

class DeleteDepreciationGroupAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationGroup $depreciationGroup): void
    {
        $this->entityManager->remove($depreciationGroup);
        $this->entityManager->flush();
    }
}
