<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class AddLocationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, string $name, int $code): void
    {
        $location = new Location($entity, $name, $code);

        $this->entityManager->persist($location);
        $entity->getLocations()->add($location);

        $this->entityManager->flush();
    }
}
