<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Components\MovementGenerator;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\DepreciationPlanProvider;
use Doctrine\ORM\EntityManagerInterface;

class CreateAssetAction
{
    private EntityManagerInterface $entityManager;
    private DepreciationPlanProvider $depreciationPlanProvider;
    private MovementGenerator $movementGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        DepreciationPlanProvider $depreciationPlanProvider,
        MovementGenerator $movementGenerator,
    ) {
        $this->entityManager = $entityManager;
        $this->depreciationPlanProvider = $depreciationPlanProvider;
        $this->movementGenerator = $movementGenerator;
    }

    public function __invoke(AccountingEntity $entity, CreateAssetRequest $request): void
    {
        $asset = new Asset($entity, $request);
        $this->entityManager->persist($asset);
        $entity->getAssets()->add($asset);
        $this->depreciationPlanProvider->createDepreciationPlan($asset);

        if ($asset->isIncluded()) {
            $this->movementGenerator->createInclusionMovement($asset);
        }
        if ($asset->getDisposalDate()) {
            $this->movementGenerator->createDisposalMovement($asset);
        }
        if ($request->increasedEntryPrice !== $request->entryPrice) {
            $this->movementGenerator->createEntryPriceChangeMovement($asset, $request, false);
        }

        $this->entityManager->flush();
    }
}
