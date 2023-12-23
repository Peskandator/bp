<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Majetek\Requests\CreateCategoryRequest;
use App\Majetek\Requests\CreateDepreciationGroupRequest;
use Doctrine\ORM\EntityManagerInterface;

class AddDepreciationGroupAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, CreateDepreciationGroupRequest $request): void
    {
        $depreciationGroup = new DepreciationGroup(
            $entity,
            $request->method,
            $request->group,
            $request->prefix,
            $request->years,
            $request->months,
            $request->rateFormat,
            $request->rateFirstYear,
            $request->rate,
            $request->rateIncreasedPrice,
        );
        $this->entityManager->persist($depreciationGroup);
        $entity->getDepreciationGroups()->add($depreciationGroup);

        $this->entityManager->flush();
    }
}
