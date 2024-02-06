<?php

namespace App\Majetek\Action;

use App\Entity\Movement;
use App\Majetek\Components\MovementGenerator;
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



        $this->entityManager->flush();
    }
}
