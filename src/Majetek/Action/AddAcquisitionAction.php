<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Disposal;
use Doctrine\ORM\EntityManagerInterface;

class AddAcquisitionAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, string $name, int $code, bool $isDisposal): void
    {
        if ($isDisposal) {
            $disposal = new Disposal($entity, $name, $code);
            $this->entityManager->persist($disposal);
            $entity->getDisposals()->add($disposal);
        }
        else {
            $acquisition = new Acquisition($entity, $name, $code);
            $this->entityManager->persist($acquisition);
            $entity->getAcquisitions()->add($acquisition);
        }

        $this->entityManager->flush();
    }
}
