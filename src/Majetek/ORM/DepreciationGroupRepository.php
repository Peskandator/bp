<?php

namespace App\Majetek\ORM;

use App\Entity\DepreciationGroup;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationGroupRepository
{
    private DepreciationGroup|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(DepreciationGroup::class);
    }

    public function find(?int $id): ?DepreciationGroup
    {
        if (!$id) {
            return null;
        }

        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
