<?php

namespace App\Majetek\Action;

use App\Entity\AssetType;
use Doctrine\ORM\EntityManagerInterface;

class EditAssetTypeAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AssetType $assetType, int $series, int $step): void
    {
        $assetType->update($series, $step);
        $this->entityManager->flush();
    }
}
