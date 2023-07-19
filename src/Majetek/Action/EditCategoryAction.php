<?php

namespace App\Majetek\Action;

use App\Entity\Category;
use App\Majetek\Requests\CreateCategoryRequest;
use Doctrine\ORM\EntityManagerInterface;

class EditCategoryAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Category $category, CreateCategoryRequest $request): void
    {
//        $placeLocation = $place->getLocation();
//        if ($placeLocation->getId() !== $location->getId()) {
//            $placeLocation->getPlaces()->removeElement($place);
//            $location->getPlaces()->add($place);
//        }

        $category->update($request);
        $this->entityManager->flush();
    }
}
