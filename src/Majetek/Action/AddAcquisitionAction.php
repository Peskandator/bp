<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
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
        $acquisition = new Acquisition($entity, $name, $code, $isDisposal);

        $this->entityManager->persist($acquisition);
        $entity->getAcquisitionsAndDisposals()->add($acquisition);

        $this->entityManager->flush();
    }
}