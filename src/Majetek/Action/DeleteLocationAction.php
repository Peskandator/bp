<?php

namespace App\Majetek\Action;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class DeleteLocationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Location $location): void
    {
        $this->entityManager->remove($location);
        $this->entityManager->flush();
    }
}
