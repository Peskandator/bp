<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Asset;
use App\Majetek\Requests\CreateAssetRequest;
use Doctrine\ORM\EntityManagerInterface;

class EditAssetAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, Asset $asset, CreateAssetRequest $request): void
    {
        $asset->update($request);
        $this->entityManager->flush();
    }
}
