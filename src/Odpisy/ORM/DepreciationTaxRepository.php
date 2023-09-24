<?php

namespace App\Odpisy\ORM;

use App\Entity\DepreciationTax;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationTaxRepository
{
    private DepreciationTax|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(DepreciationTax::class);
    }

    public function find($id): ?DepreciationTax
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
