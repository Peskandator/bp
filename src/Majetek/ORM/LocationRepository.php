<?php

namespace App\Majetek\ORM;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class LocationRepository
{
    private Location|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Location::class);
    }

    public function find(?int $id): ?Location
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
