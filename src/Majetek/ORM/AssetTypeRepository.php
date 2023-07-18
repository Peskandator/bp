<?php

namespace App\Majetek\ORM;

use App\Entity\AssetType;
use Doctrine\ORM\EntityManagerInterface;

class AssetTypeRepository
{
    private AssetType|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(AssetType::class);
    }

    public function find(?int $id): ?AssetType
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
