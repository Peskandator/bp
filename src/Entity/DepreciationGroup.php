<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="depreciation_group")
 */
class DepreciationGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="group_number", type="integer")
     */
    private int $group;
    /**
     * @ORM\Column(name="depreciation_method", type="integer")
     */
    private int $method;
    /**
     * @ORM\Column(name="years_to_depreciate", type="integer", nullable=true)
     */
    private ?int $years;
    /**
     * @ORM\Column(name="months_to_depreciate", type="integer", nullable=true)
     */
    private ?int $months;
    /**
     * @ORM\Column(name="is_coefficient", type="boolean")
     */
    private bool $isCoefficient;
    /**
     * @ORM\Column(name="rate_first_year", type="float")
     */
    private float $rateFirstYear;
    /**
     * @ORM\Column(name="rate", type="float")
     */
    private float $rate;
    /**
     * @ORM\Column(name="rate_increased_price", type="float")
     */
    private float $rateIncreasedPrice;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="depreciationGroups")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;


    public function __construct(
        AccountingEntity $entity,
        int $method,
        int $group,
        ?int $years,
        ?int $months,
        bool $isCoefficient,
        float $rateFirstYear,
        float $rate,
        float $rateIncreasedPrice,
    ){
        $this->entity = $entity;
        $this->group = $group;
        $this->method = $method;
        $this->years = $years;
        $this->months = $months;
        $this->isCoefficient = $isCoefficient;
        $this->rateFirstYear = $rateFirstYear;
        $this->rate = $rate;
        $this->rateIncreasedPrice = $rateIncreasedPrice;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): int
    {
        return $this->group;
    }

    public function getMethod(): int
    {
        return $this->method;
    }

    public function getYears(): ?int
    {
        return $this->years;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function isCoefficient(): bool
    {
        return $this->isCoefficient;
    }

    public function getRateFirstYear(): int
    {
        return $this->rateFirstYear;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function getRateIncreasedPrice(): int
    {
        return $this->rateIncreasedPrice;
    }
}
