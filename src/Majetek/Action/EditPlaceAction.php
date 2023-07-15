<?php

namespace App\Majetek\Action;

use App\Entity\Location;
use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;

class EditPlaceAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Place $place, string $name, int $code, Location $location): void
    {
        $place->update($name, $code, $location);

        // TODO: remove place from location ???

        $this->entityManager->flush();
    }
}
