<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="name", type="string")
     */
    private ?string $name;
    /**
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private int $code;
    /**
     * @ORM\Column(name="account_asset", type="string", nullable=true)
     */
    private ?string $accountAsset;
    /**
     * @ORM\Column(name="account_depreciation", type="string", nullable=true)
     */
    private ?string $accountDepreciation;
    /**
     * @ORM\Column(name="account_repairs", type="string", nullable=true)
     */
    private ?string $accountRepairs;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="categories")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;
    /**
     * @ORM\ManyToOne(targetEntity="DepreciationGroup")
     * @ORM\JoinColumn(name="depreciation_group_id", referencedColumnName="id", nullable=true)
     */
    private ?DepreciationGroup $depreciationGroup;


    public function __construct(
        AccountingEntity $entity,
        int $code,
        ?string $name,
        ?DepreciationGroup $depreciationGroup,
        ?string $accountAsset,
        ?string $accountDepreciation,
        ?string $accountRepairs
    ){
        $this->entity = $entity;
        $this->code = $code;
        $this->name = $name;
        $this->depreciationGroup = $depreciationGroup;
        $this->accountAsset = $accountAsset;
        $this->accountDepreciation = $accountDepreciation;
        $this->accountRepairs = $accountRepairs;
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

    public function getAccountAsset(): ?string
    {
        return $this->accountAsset;
    }

    public function getAccountDepreciation(): ?string
    {
        return $this->accountDepreciation;
    }

    public function getAccountRepairs(): ?string
    {
        return $this->accountRepairs;
    }

    public function getDepreciationGroup(): ?DepreciationGroup
    {
        return $this->depreciationGroup;
    }
}
