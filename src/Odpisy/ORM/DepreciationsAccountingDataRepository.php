<?php

namespace App\Odpisy\ORM;

use App\Entity\DepreciationsAccountingData;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationsAccountingDataRepository
{
    private DepreciationsAccountingData|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(DepreciationsAccountingData::class);
    }

    public function find($id): ?DepreciationsAccountingData
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
