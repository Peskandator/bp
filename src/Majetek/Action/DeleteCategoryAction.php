<?php

namespace App\Majetek\Action;

use App\Entity\Acquisition;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class DeleteCategoryAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
