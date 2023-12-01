<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationAccounting;
use App\Odpisy\Requests\EditDepreciationRequest;
use Doctrine\ORM\EntityManagerInterface;

class EditAccountingDepreciationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationAccounting $depreciation, EditDepreciationRequest $request): void
    {
        // TODO: přepočítat
    }
}
