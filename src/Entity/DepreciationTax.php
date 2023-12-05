<?php

namespace App\Entity;

use App\Majetek\Enums\DepreciationMethod;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Requests\CreateDepreciationRequest;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="depreciation_tax")
 */
class DepreciationTax implements Depreciation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private ?int $year;
    /**
     * @ORM\Column(name="depreciation_year", type="integer", nullable=true)
     */
    private ?int $depreciationYear;
    /**
     * @ORM\Column(name="depreciation_method", type="integer", nullable=false)
     */
    private int $method;
    /**
     * @ORM\Column(name="executable", type="boolean")
     */
    private bool $executable;
    /**
     * @ORM\Column(name="executed", type="boolean")
     */
    private bool $executed;
    /**
     * @ORM\Column(name="accounted", type="boolean", nullable=false)
     */
    private bool $accounted;
    /**
     * @ORM\Column(name="percentage", type="float", nullable=false)
     */
    private float $percentage;
    /**
     * @ORM\Column(name="is_coefficient", type="boolean")
     */
    private bool $isCoefficient;
    /**
     * @ORM\Column(name="rate", type="float", nullable=false)
     */
    private float $rate;
    /**
     * @ORM\Column(name="entry_price", type="float", nullable=false)
     */
    private float $entryPrice;
    /**
     * @ORM\Column(name="increased_entry_price", type="float", nullable=true)
     */
    private ?float $increasedEntryPrice;
    /**
     * @ORM\Column(name="depreciation_amount", type="float", nullable=false)
     */
    private float $depreciationAmount;
    /**
     * @ORM\Column(name="depreciated_amount", type="float", nullable=false)
     */
    private float $depreciatedAmount;
    /**
     * @ORM\Column(name="residual_price", type="float", nullable=false)
     */
    private float $residualPrice;
    /**
     * @ORM\Column(name="disposal_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $disposalDate;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset", inversedBy="depreciationsTax", inversedBy="assets")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id", nullable=false)
     */
    private Asset $asset;
    /**
     * @ORM\ManyToOne(targetEntity="DepreciationGroup", inversedBy="depreciationsTax")
     * @ORM\JoinColumn(name="depreciation_group_id", referencedColumnName="id", nullable=false)
     */
    private DepreciationGroup $depreciationGroup;


    public function __construct(
    ){
    }

    public function updateFromRequest(
        CreateDepreciationRequest $request
    ){
        $this->asset = $request->asset;
        $this->depreciationGroup = $request->depreciationGroup;
        $this->executable = $request->executable;
        $this->percentage = $request->percentage;
        $this->depreciationAmount = $request->depreciationAmount;
        $this->depreciatedAmount = $request->depreciatedAmount;
        $this->residualPrice = $request->residualPrice;
        $this->executed = false;
        $this->accounted = false;
        $this->entryPrice = $request->asset->getEntryPriceTax();
        $this->increasedEntryPrice = $request->asset->getIncreasedEntryPriceTax();
        $this->depreciationYear = $request->depreciationYear;
        $this->depreciatedAmount = $request->depreciatedAmount;
        $this->year = $request->year;
        $this->method = $request->depreciationGroup->getMethod();
        $this->isCoefficient = $request->depreciationGroup->isCoefficient();
        $this->rate = $request->rate;
        $this->disposalDate = $request->asset->getDisposalDate();
    }

    public function update(
        Asset $asset,
        DepreciationGroup $depreciationGroup,
        int $year,
        int $depreciationYear,
        float $depreciationAmount,
        float $percentage,
        float $depreciatedAmount,
        float $residualPrice,
        bool $executable,
        float $rate
    ): void
    {
        $this->asset = $asset;
        $this->depreciationGroup = $depreciationGroup;
        $this->executable = $executable;
        $this->percentage = $percentage;
        $this->depreciationAmount = $depreciationAmount;
        $this->depreciatedAmount = $depreciatedAmount;
        $this->residualPrice = $residualPrice;
        $this->executed = false;
        $this->entryPrice = $asset->getEntryPriceTax();
        $this->increasedEntryPrice = $asset->getIncreasedEntryPriceTax();
        $this->depreciationYear = $depreciationYear;
        $this->depreciatedAmount = $depreciatedAmount;
        $this->year = $year;
        $this->method = $depreciationGroup->getMethod();
        $this->isCoefficient = $depreciationGroup->isCoefficient();
        $this->rate = $rate;
    }

    public function updateNotExecutable(float $depreciatedAmount, float $residualPrice): void
    {
        $this->depreciatedAmount = $depreciatedAmount;
        $this->residualPrice = $residualPrice;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAsset(): Asset
    {
        return $this->asset;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->asset->getEntity();
    }

    public function getDepreciationGroup(): DepreciationGroup
    {
        return $this->depreciationGroup;
    }

    public function getResidualPrice(): float
    {
        return $this->residualPrice;
    }

    public function getEntryPrice(): float
    {
        return $this->entryPrice;
    }

    public function getIncreasedEntryPrice(): ?float
    {
        return $this->increasedEntryPrice;
    }

    public function getDepreciationAmount(): float
    {
        return $this->depreciationAmount;
    }

    public function getDepreciatedAmount(): float
    {
        return $this->depreciatedAmount;
    }

    public function getDepreciationYear(): int
    {
        return $this->depreciationYear;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getPercentage(): float
    {
        return $this->percentage;
    }

    public function isExecutable(): bool
    {
        return $this->executable;
    }

    public function isExecuted(): bool
    {
        return $this->executed;
    }

    public function isAccounted(): bool
    {
        return $this->accounted;
    }

    public function getMethod(): int
    {
        return $this->method;
    }

    public function getMethodText(): string
    {
        $methodTexts = DepreciationMethod::NAMES;
        return $methodTexts[$this->getMethod()];
    }

    public function isCoefficient(): bool
    {
        return $this->isCoefficient;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDisposalDate(): ?\DateTimeInterface
    {
        return $this->disposalDate;
    }

    public function isAccountingDepreciation(): bool
    {
        return false;
    }

    public function getBaseDepreciationAmount(EditDepreciationCalculator $calculator): float
    {
        return $calculator->getBaseDepreciationAmountTax($this);
    }
}
