<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\DepreciationCalculator;
use Doctrine\ORM\EntityManagerInterface;

class CreateAssetAction
{
    private EntityManagerInterface $entityManager;
    private DepreciationCalculator $depreciationCalculator;

    public function __construct(
        EntityManagerInterface $entityManager,
        DepreciationCalculator $depreciationCalculator
    ) {
        $this->entityManager = $entityManager;
        $this->depreciationCalculator = $depreciationCalculator;
    }

    public function __invoke(AccountingEntity $entity, CreateAssetRequest $request): void
    {
        $asset = new Asset($entity, $request);
        $this->entityManager->persist($asset);
        $entity->getAssets()->add($asset);
        $this->entityManager->flush();

        $this->depreciationCalculator->createDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
