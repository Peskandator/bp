<?php

namespace App\Majetek\ORM;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class AssetRepository
{
    private Asset|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Asset::class);
    }

    public function find(?int $id): ?Asset
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
