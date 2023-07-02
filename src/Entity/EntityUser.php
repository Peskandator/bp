<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="entity_user")
 */
class EntityUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\ManyToOne(targetEntity="AccountingEntity", inversedBy="entityUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private AccountingEntity $accountingEntity;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="entityUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;


    public function __construct(
        User $user,
        AccountingEntity $accountingEntity,
    ) {
        $this->accountingEntity = $accountingEntity;
        $this->user = $user;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->accountingEntity;
    }
}
