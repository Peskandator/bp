<?php

namespace App\Majetek\ORM;

use App\Entity\Disposal;
use Doctrine\ORM\EntityManagerInterface;

class DisposalRepository
{
    private Disposal|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Disposal::class);
    }

    public function find(?int $id): ?Disposal
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

    public function findDefaults(): array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select('e')
            ->from(Disposal::class, 'e')
            ->where('e.isDefault = 1')
            ->orderBy('e.id')
        ;
        return $builder->getQuery()->getResult();
    }
}
