<?php

namespace App\Majetek\Action;

use App\Entity\Location;
use App\Entity\Movement;
use App\Entity\Place;
use App\Majetek\Components\MovementGenerator;
use App\Majetek\Enums\MovementType;
use Doctrine\ORM\EntityManagerInterface;

class EditMovementAction
{
    private EntityManagerInterface $entityManager;
    private MovementGenerator $movementGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        MovementGenerator $movementGenerator,
    ) {
        $this->entityManager = $entityManager;
        $this->movementGenerator = $movementGenerator;
    }

    public function __invoke(Movement $movement, string $description, ?\DateTimeInterface $date, string $accDebited, string $accCredited): void
    {
        $asset = $movement->getAsset();
        if ($movement->getType() === MovementType::INCLUSION) {
            $asset->setEntryDate($date);
        }
        if ($movement->getType() === MovementType::DISPOSAL) {
            $asset->setDisposalDate($date);
        }

        $movement->edit($description, $date, $accDebited, $accCredited);
        $this->movementGenerator->regenerateResidualPricesForPriceChangeMovements($asset);
        $this->entityManager->flush();
    }
}
