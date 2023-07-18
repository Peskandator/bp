<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\Acquisition;
use App\Entity\Category;
use App\Majetek\Requests\CreateCategoryRequest;
use Doctrine\ORM\EntityManagerInterface;

class AddCategoryAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(AccountingEntity $entity, CreateCategoryRequest $request): void
    {
        $category = new Category
        (
            $entity,
            $request->code,
            $request->name,
            $request->depreciationGroup,
            $request->accountAsset,
            $request->accountDepreciation,
            $request->accountRepairs,
            $request->isDepreciable
        );

        $this->entityManager->persist($category);
        $entity->getCategories()->add($category);

        $this->entityManager->flush();
    }
}
