<?php

namespace App\Majetek\Action;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class DeleteAssetAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Asset $asset): void
    {
        $taxDepreciations = $asset->getTaxDepreciations();
        $accountingDepreciations = $asset->getAccountingDepreciations();
        $movements = $asset->getMovements();

        foreach ($taxDepreciations as $depreciation) {
            $this->entityManager->remove($depreciation);
        }
        $asset->clearTaxDepreciations();
        foreach ($accountingDepreciations as $depreciation) {
            $this->entityManager->remove($depreciation);
        }
        $asset->clearAccountingDepreciations();
        foreach ($movements as $movement) {
            $this->entityManager->remove($movement);
        }
        $asset->clearMovements();

        $this->entityManager->remove($asset);
        $this->entityManager->flush();
    }
}
