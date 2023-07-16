<?php

namespace App\Majetek\Action;

use App\Entity\AccountingEntity;
use App\Entity\AssetType;
use App\Entity\Category;
use App\Entity\DepreciationGroup;
use App\Entity\EntityUser;
use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\Requests\CreateEntityRequest;
use App\Utils\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;

class CreateEntityAction
{
    private EntityManagerInterface $entityManager;
    private CurrentUser $currentUser;

    public function __construct(
        EntityManagerInterface $entityManager,
        CurrentUser $currentUser
    ) {
        $this->entityManager = $entityManager;
        $this->currentUser = $currentUser;
    }

    public function __invoke(CreateEntityRequest $request): int
    {
        $user = $this->currentUser->getCurrentLoggedInUser();

        $entity = new AccountingEntity($request);
        $this->entityManager->persist($entity);

        $entityUser = new EntityUser($user, $entity, true);
        $this->entityManager->persist($entityUser);

        $user->getEntityUsers()->add($entityUser);
        $entity->getEntityUsers()->add($entityUser);

        $this->createDialsDefaults($entity);

        $this->entityManager->flush();

        return $entity->getId();
    }

    protected function createDialsDefaults(AccountingEntity $entity): void
    {
        $this->entityManager->persist(new AssetType($entity, 1, 'Odpisovaný', 10000000, 1));
        $this->entityManager->persist(new AssetType($entity, 1, 'Odpisovaný', 20000000, 1));
        $this->entityManager->persist(new AssetType($entity, 1, 'Odpisovaný', 30000000, 1));
        $this->entityManager->persist(new AssetType($entity, 1, 'Odpisovaný', 40000000, 1));

        $depreciationGroup1 = new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 1, 3, null, false,20, 40, 33.3);
        $depreciationGroup2 = new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 2, 5, null, false,11, 22.25, 20);
        $depreciationGroup3 = new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 3, 10, null, false,5.5, 10.5, 10);
        $depreciationGroup4 = new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 4, 20, null, false,2.15, 5.15, 5);
        $depreciationGroup5 = new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 5, 30, null, false,1.4, 3.4, 3.4);
        $this->entityManager->persist($depreciationGroup1);
        $this->entityManager->persist($depreciationGroup2);
        $this->entityManager->persist($depreciationGroup3);
        $this->entityManager->persist($depreciationGroup4);
        $this->entityManager->persist($depreciationGroup5);

        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::UNIFORM, 6, 50, null, false,1.02, 2.02, 2));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,3, 4, 3));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,5, 6, 5));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,10, 11, 10));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,20, 21, 20));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,30, 31, 30));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::ACCELERATED, 1, 50, null, true,50, 51, 50));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::EXTRAORDINARY, 1, null, 12, false,100, 0, 0));
        $this->entityManager->persist(new DepreciationGroup($entity, DepreciationMethod::EXTRAORDINARY, 2, null, 24, false,1, 0, 0));

        $this->entityManager->persist(new Category($entity, 1, 'Budovy, stavby', $depreciationGroup1, '021000', '551000', '081000'));
        $this->entityManager->persist(new Category($entity, 2, 'Dopravní prostř.', $depreciationGroup2, '022000', '551000', '082000'));
        $this->entityManager->persist(new Category($entity, 3, 'Stroje, nástroje', $depreciationGroup3, '022000', '551000', '082000'));
        $this->entityManager->persist(new Category($entity, 4, 'Pozemky', null, '031000', null, null));
        $this->entityManager->persist(new Category($entity, 5, 'TZ na pron. majetku', $depreciationGroup5, '021000', '551000', '082000'));
        $this->entityManager->persist(new Category($entity, 6, 'Leasing', null, '501300', null, null));
        $this->entityManager->persist(new Category($entity, 7, 'Drobný HM', null, '501300', null, null));
    }
}
