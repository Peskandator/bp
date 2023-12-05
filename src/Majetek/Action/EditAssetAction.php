<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\DepreciationPlanProvider;
use Doctrine\ORM\EntityManagerInterface;

class EditAssetAction
{
    private EntityManagerInterface $entityManager;
    private DepreciationPlanProvider $depreciationPlanProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        DepreciationPlanProvider $depreciationPlanProvider
    ) {
        $this->entityManager = $entityManager;
        $this->depreciationPlanProvider = $depreciationPlanProvider;
    }

    public function __invoke(AccountingEntity $entity, Asset $asset, CreateAssetRequest $request): void
    {
        $asset->update($request);
        $this->depreciationPlanProvider->createDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
