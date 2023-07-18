<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="asset_type")
 */
class AssetType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="code", type="integer")
     */
    private int $code;
    /**
     * @ORM\Column(name="name", type="string")
     */
    private ?string $name;
    /**
     * @ORM\Column(name="series", type="integer")
     */
    private int $series;
    /**
     * @ORM\Column(name="step", type="integer")
     */
    private int $step;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="assetTypes")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;

    public function __construct(
        AccountingEntity $entity,
        int $code,
        string $name,
        int $series,
        int $step
    ){
        $this->entity = $entity;
        $this->name = $name;
        $this->code = $code;
        $this->series = $series;
        $this->step = $step;
    }

    public function update(int $series, int $step): void
    {
        $this->series = $series;
        $this->step = $step;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getSeries(): int
    {
        return $this->series;
    }

    public function getStep(): int
    {
        return $this->step;
    }
}
