<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use Doctrine\ORM\EntityManagerInterface;

class EditAcquisitionAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Acquisition $acquisition, string $name, int $code, bool $isDisposal): void
    {
        $acquisition->update($name, $code, $isDisposal);
        $this->entityManager->flush();
    }
}
