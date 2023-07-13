<?php

namespace App\Majetek\ORM;

use App\Entity\Acquisition;
use Doctrine\ORM\EntityManagerInterface;

class AcquisitionRepository
{
    private Acquisition|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Acquisition::class);
    }

    public function find($id): ?Acquisition
    {
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
            ->from(Acquisition::class, 'e')
            ->where('e.isDefault = 1')
            ->orderBy('e.id')
        ;
        return $builder->getQuery()->getResult();
    }
}
