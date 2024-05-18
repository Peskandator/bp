<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationsAccountingData;
use Doctrine\ORM\EntityManagerInterface;

class RegenerateDepreciationsAccountingDataAction
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationsAccountingData $data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
