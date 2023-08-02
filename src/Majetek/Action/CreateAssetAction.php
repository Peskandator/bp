<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Requests\CreateAssetRequest;
use Doctrine\ORM\EntityManagerInterface;

class CreateAssetAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, CreateAssetRequest $request): void
    {
        $asset = new Asset($entity, $request);
        $this->entityManager->persist($asset);
        $entity->getAssets()->add($asset);
        $this->entityManager->flush();
    }
}
