<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="accounting_entity")
 */
class AccountingEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private ?string $name;
    /**
     * @ORM\OneToMany(targetEntity="EntityUser", mappedBy="accountingEntity")
     */
    private Collection $entityUsers;


    public function __construct(
        string $name,
        Collection $entityUsers,
    ){
        $this->name = $name;
        $this->entityUsers = $entityUsers;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEntityUsers(): Collection
    {
        return $this->entityUsers;
    }
}
