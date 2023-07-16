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
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private ?string $name;
    /**
     * @ORM\Column(name="code", type="integer")
     */
    private int $code;
    /**
     * @ORM\Column(name="method", type="integer", nullable=false)
     */
    private int $method;
    /**
     * @ORM\Column(name="years", type="integer")
     */
    private int $years;
    /**
     * @ORM\Column(name="months", type="integer")
     */
    private int $months;
    /**
     * @ORM\Column(name="coefficient", type="integer")
     */
    private int $coefficient;
    /**
     * @ORM\Column(name="rate_first_year", type="integer")
     */
    private int $rateFirstYear;
    /**
     * @ORM\Column(name="rate", type="integer")
     */
    private int $rate;
    /**
     * @ORM\Column(name="rate_increased_price", type="integer")
     */
    private int $rateIncreasedPrice;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="depreciationGroups")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;


    public function __construct(
        AccountingEntity $entity,
        int $code,
        string $name,
        int $method,
        int $years,
        int $months,
        int $coefficient,
        int $rateFirstYear,
        int $rate,
        int $rateIncreasedPrice,
    ){
        $this->entity = $entity;
        $this->code = $code;
        $this->name = $name;
        $this->method = $method;
        $this->years = $years;
        $this->months = $months;
        $this->coefficient = $coefficient;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMethod(): int
    {
        return $this->method;
    }

    public function getYears(): int
    {
        return $this->years;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function getCoefficient(): int
    {
        return $this->coefficient;
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
