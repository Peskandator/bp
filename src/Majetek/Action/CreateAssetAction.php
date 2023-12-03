<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\DepreciationPlanProvider;
use Doctrine\ORM\EntityManagerInterface;

class CreateAssetAction
{
    private EntityManagerInterface $entityManager;
    private DepreciationPlanProvider $depreciationPlanProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        DepreciationPlanProvider $depreciationPlanProvider,
    ) {
        $this->entityManager = $entityManager;
        $this->depreciationPlanProvider = $depreciationPlanProvider;
    }

    public function __invoke(AccountingEntity $entity, CreateAssetRequest $request): void
    {
        $asset = new Asset($entity, $request);
        $this->entityManager->persist($asset);
        $entity->getAssets()->add($asset);
        $this->entityManager->flush();

        $this->depreciationPlanProvider->createDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
