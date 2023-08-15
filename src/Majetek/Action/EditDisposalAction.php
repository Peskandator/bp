<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Disposal;
use Doctrine\ORM\EntityManagerInterface;

class EditDisposalAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Disposal $disposal, string $name, int $code): void
    {
        $disposal->update($name, $code);
        $this->entityManager->flush();
    }
}
