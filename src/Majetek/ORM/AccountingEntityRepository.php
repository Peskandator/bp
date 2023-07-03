<?php

namespace App\Majetek\ORM;

use App\Entity\AccountingEntity;
use Doctrine\ORM\EntityManagerInterface;

class AccountingEntityRepository
{
    private AccountingEntity|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(AccountingEntity::class);
    }

    public function find($id): ?AccountingEntity
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }


}
