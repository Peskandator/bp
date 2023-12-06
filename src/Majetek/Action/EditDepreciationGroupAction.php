<?php

namespace App\Majetek\Action;

use App\Entity\Asset;
use App\Entity\DepreciationGroup;
use App\Majetek\Requests\CreateDepreciationGroupRequest;
use App\Odpisy\Components\EditDepreciationCalculator;
use Doctrine\ORM\EntityManagerInterface;

class EditDepreciationGroupAction
{
    private EntityManagerInterface $entityManager;
    private EditDepreciationCalculator $editDepreciationCalculator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EditDepreciationCalculator $editDepreciationCalculator
    ) {
        $this->entityManager = $entityManager;
        $this->editDepreciationCalculator = $editDepreciationCalculator;
    }

    public function __invoke(DepreciationGroup $depreciationGroup, CreateDepreciationGroupRequest $request): void
    {
        $depreciationGroup->update($request);
        $this->entityManager->flush();

        $assets = $depreciationGroup->getEntity()->getAssets();
        /**
         * @var Asset $asset
         */
        foreach ($assets as $asset) {
            if ($asset->getDepreciationGroupTax() || $asset->getDepreciationGroupAccounting()) {
                $this->editDepreciationCalculator->updateDepreciationPlan($asset);
            }
        }
    }
}
