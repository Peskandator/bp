<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="disposal")
 */
class Disposal
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
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private ?int $code;
    /**
     * @ORM\Column(name="is_default", type="boolean")
     */
    private bool $isDefault;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="locations")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=true)
     */
    private ?AccountingEntity $entity;

    public function __construct(
        AccountingEntity $entity,
        string $name,
        int $code,
    ){
        $this->entity = $entity;
        $this->name = $name;
        $this->code = $code;
        $this->isDefault = false;
    }

    public function update(string $name, int $code): void
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
