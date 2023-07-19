<?php

namespace App\Majetek\Action;

use App\Entity\DepreciationGroup;
use App\Majetek\Requests\CreateDepreciationGroupRequest;
use Doctrine\ORM\EntityManagerInterface;

class EditDepreciationGroupAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationGroup $depreciationGroup, CreateDepreciationGroupRequest $request): void
    {
        $depreciationGroup->update($request);
        $this->entityManager->flush();
    }
}
