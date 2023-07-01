<?php

namespace App\User\ORM;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    private User|\Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function find($id): ?User
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findAllOrderByEmail(): array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select('e')
            ->from(User::class, 'e')
            ->orderBy('e.email', 'asc')
        ;
        return $builder->getQuery()->getResult();
    }

    public function findByUsername(string $username): ?User
    {
        return $this->repository->findOneBy(['username' => $username]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findByVerificationToken(string $code): ?User
    {
        return $this->repository->findOneBy(['verificationToken' => $code]);
    }

    public function findByOneTimeLoginToken(string $code): ?User
    {
        return $this->repository->findOneBy(['oneTimeLoginToken' => $code]);
    }

    public function findByResetPasswordToken(string $code): ?User
    {
         return $this->repository->findOneBy(['resetPasswordToken' => $code]);
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->repository->findOneBy(['phone' => $phone]);
    }
}
