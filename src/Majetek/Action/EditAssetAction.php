<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Components\MovementGenerator;
use App\Majetek\Requests\CreateAssetRequest;
use App\Odpisy\Components\EditDepreciationCalculator;
use Doctrine\ORM\EntityManagerInterface;

class EditAssetAction
{
    private EntityManagerInterface $entityManager;
    private EditDepreciationCalculator $editDepreciationCalculator;
    private MovementGenerator $movementGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EditDepreciationCalculator $editDepreciationCalculator,
        MovementGenerator $movementGenerator,
    ) {
        $this->entityManager = $entityManager;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
        $this->movementGenerator = $movementGenerator;
    }

    public function __invoke(AccountingEntity $entity, Asset $asset, CreateAssetRequest $request): void
    {
        if (!$asset->isIncluded() && $request->isIncluded) {
            $this->movementGenerator->createEntryPriceChangeMovement($asset, $request, true);
            $asset->update($request);
        } else {
            if ($asset->isIncluded()) {
                $this->movementGenerator->generateEntryPriceChangeMovement($asset, $request);
                $this->movementGenerator->generateInfoChangeMovements($asset, $request);
                $asset->update($request);
                $this->movementGenerator->regenerateResidualPricesForPriceChangeMovements($asset);
            } else {
                $asset->update($request);
            }
        }
        $this->movementGenerator->regenerateMovementsAfterAssetEdit($asset);
        $this->editDepreciationCalculator->updateDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
