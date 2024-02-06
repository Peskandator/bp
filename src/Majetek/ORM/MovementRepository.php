<?php

namespace App\Majetek\ORM;

use App\Entity\Movement;
use Doctrine\ORM\EntityManagerInterface;

class MovementRepository
{
    private Movement|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Movement::class);
    }

    public function find($id): ?Movement
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
