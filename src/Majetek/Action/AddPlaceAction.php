<?php

namespace App\Majetek\Action;

use App\Entity\Place;
use App\Majetek\ORM\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddPlaceAction
{
    private EntityManagerInterface $entityManager;
    private LocationRepository $locationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LocationRepository $locationRepository

    ) {
        $this->entityManager = $entityManager;
        $this->locationRepository = $locationRepository;
    }

    public function __invoke(int $locationId, string $name, int $code): void
    {
        $location = $this->locationRepository->find($locationId);
        $place = new Place($location, $name, $code);

        $this->entityManager->persist($place);
        $location->getPlaces()->add($place);

        $this->entityManager->flush();
    }
}
