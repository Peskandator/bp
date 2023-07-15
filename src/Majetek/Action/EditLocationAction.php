<?php

namespace App\Majetek\Action;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class EditLocationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Location $location, string $name, int $code): void
    {
        $location->update($name, $code);
        $this->entityManager->flush();
    }
}
