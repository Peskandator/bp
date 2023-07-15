<?php

namespace App\Majetek\Action;

use App\Entity\Acquisition;
use Doctrine\ORM\EntityManagerInterface;

class DeleteAcquisitionAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Acquisition $acquisition): void
    {
        $this->entityManager->remove($acquisition);
        $this->entityManager->flush();
    }
}
