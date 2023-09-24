<?php

namespace App\Odpisy\ORM;

use App\Entity\DepreciationAccounting;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationAccountingRepository
{
    private DepreciationAccounting|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(DepreciationAccounting::class);
    }

    public function find($id): ?DepreciationAccounting
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
