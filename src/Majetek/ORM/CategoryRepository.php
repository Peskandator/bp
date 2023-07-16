<?php

namespace App\Majetek\ORM;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryRepository
{
    private Category|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function find($id): ?Category
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
