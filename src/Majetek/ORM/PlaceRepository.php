<?php

namespace App\Majetek\ORM;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;

class PlaceRepository
{
    private Place|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Place::class);
    }

    public function find($id): ?Place
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }


}
