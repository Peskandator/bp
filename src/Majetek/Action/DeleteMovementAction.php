<?php

namespace App\Majetek\Action;

use App\Entity\Movement;
use App\Majetek\Components\MovementGenerator;
use App\Majetek\Enums\MovementType;
use App\Odpisy\Components\EditDepreciationCalculator;
use Doctrine\ORM\EntityManagerInterface;

class DeleteMovementAction
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

    public function __invoke(?Movement $movement): void
    {
        $asset = $movement->getAsset();
        $asset->getMovements()->removeElement($movement);
        $this->entityManager->remove($movement);
        if ($movement->getType() === MovementType::DISPOSAL) {
            $asset->setDisposalDate(null);
        } else if ($movement->getType() === MovementType::INCLUSION) {
            $asset->setIsIncluded(false);
        } else if ($movement->getType() === MovementType::DEPRECIATION_TAX || $movement->getType() === MovementType::DEPRECIATION_ACCOUNTING) {
            $depreciation = $movement->getDepreciation();
            $depreciation->setExecuted(false);
        } else if ($movement->getType() === MovementType::ENTRY_PRICE_CHANGE) {
            $asset->recalculateIncreasedEntryPrice();
            $entryPriceMovements = $asset->getMovementsWithType(MovementType::ENTRY_PRICE_CHANGE);
            $dateOfLastChange = null;
            /**
             * @var Movement $entryPriceMovement
             */
            foreach ($entryPriceMovements as $entryPriceMovement) {
                $dateOfChange = $entryPriceMovement->getDate();
                if ($dateOfLastChange === null || $dateOfChange < $dateOfLastChange) {
                    $dateOfLastChange = $dateOfChange;
                }
            }
            if ($dateOfLastChange !== null) {
                $asset->setIncreaseDate($dateOfLastChange);
            }
            $this->movementGenerator->regenerateResidualPricesForPriceChangeMovements($asset);
        }
        $this->editDepreciationCalculator->updateDepreciationPlan($asset);
        $this->entityManager->flush();
    }
}
