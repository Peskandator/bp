<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Components\MovementGenerator;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\EditDepreciationCalculator;
use Doctrine\ORM\EntityManagerInterface;

class CreateAssetAction
{
    private EntityManagerInterface $entityManager;
    private MovementGenerator $movementGenerator;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        EntityManagerInterface $entityManager,
        MovementGenerator $movementGenerator,
        EditDepreciationCalculator $editDepreciationCalculator,
    ) {
        $this->entityManager = $entityManager;
        $this->movementGenerator = $movementGenerator;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function __invoke(AccountingEntity $entity, CreateAssetRequest $request): void
    {
        $asset = new Asset($entity, $request);
        $this->entityManager->persist($asset);
        $entity->getAssets()->add($asset);
        if ($asset->isIncluded()) {
            $this->movementGenerator->createInclusionMovement($asset);

            if ($asset->getDisposalDate()) {
                $this->movementGenerator->createDisposalMovement($asset);
            }
            if ($request->increasedEntryPrice && $request->increasedEntryPrice !== $request->entryPrice) {
                $this->movementGenerator->createEntryPriceChangeMovement($asset, $request, false);
            }
        }
        $this->editDepreciationCalculator->updateDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
