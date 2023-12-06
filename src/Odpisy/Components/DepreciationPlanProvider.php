<?php
declare(strict_types=1);

namespace App\Odpisy\Components;


use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class DepreciationPlanProvider extends DepreciationCalculator
{
    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($entityManager);
    }

    public function createDepreciationPlan(Asset $asset): void
    {
        $this->updateDepreciationPlan($asset);
    }
}
