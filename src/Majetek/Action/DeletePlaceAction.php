<?php

namespace App\Majetek\Action;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;

class DeletePlaceAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Place $place): void
    {
        $location = $place->getLocation();
        $location->getPlaces()->removeElement($place);
        $this->entityManager->remove($place);
        $this->entityManager->flush();
    }
}
