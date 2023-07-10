<?php

namespace App\Majetek\ORM;

use App\Entity\EntityUser;
use Doctrine\ORM\EntityManagerInterface;

class EntityUserRepository
{
    private EntityUser|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(EntityUser::class);
    }

    public function find($id): ?EntityUser
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }


}
