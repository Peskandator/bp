<?php

namespace App\Odpisy\Action;

use App\Entity\DepreciationTax;
use App\Odpisy\Requests\EditDepreciationRequest;
use Doctrine\ORM\EntityManagerInterface;

class EditTaxDepreciationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DepreciationTax $depreciation, EditDepreciationRequest $request): void
    {
        // TODO: přepočítat
    }
}
