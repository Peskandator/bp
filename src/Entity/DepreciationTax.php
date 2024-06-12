<?php

namespace App\Entity;

use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\Enums\RateFormat;
use App\Odpisy\Components\EditDepreciationCalculator;
use App\Odpisy\Requests\UpdateDepreciationRequest;
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
     * @ORM\Column(name="percentage", type="float", nullable=false)
     */
    private float $percentage;
    /**
     * @ORM\Column(name="rate_format", type="integer", nullable=false)
     */
    private int $rateFormat;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset", inversedBy="depreciationsTax")
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
        UpdateDepreciationRequest $request
    ): void
    {
        $this->asset = $request->asset;
        $this->depreciationGroup = $request->depreciationGroup;
        $this->executable = $request->executable;
        $this->percentage = $request->percentage;
        $this->depreciationAmount = $request->depreciationAmount;
        $this->depreciatedAmount = $request->depreciatedAmount;
        $this->residualPrice = $request->residualPrice;
        $this->executed = false;
        $this->entryPrice = $request->asset->getEntryPrice();
        $this->increasedEntryPrice = $request->asset->getPriceForYear($request->year);
        $this->depreciationYear = $request->depreciationYear;
        $this->depreciatedAmount = $request->depreciatedAmount;
        $this->year = $request->year;
        $this->method = $request->depreciationGroup->getMethod();
        $this->rateFormat = $request->depreciationGroup->getRateFormat();
        $this->rate = $request->rate;
        $this->disposalDate = $request->asset->getDisposalDate();
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

    public function setExecuted(bool $value): void
    {
        $this->executed = $value;
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
        return $this->rateFormat === RateFormat::COEFFICIENT;
    }

    public function getRateFormat(): int
    {
        return $this->rateFormat;
    }

    public function getRate(): ?float
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

    public function isExecutionCancelable(): bool
    {
        $asset = $this->getAsset();
        $year = $this->getYear();
        $nextYearDepreciation = $asset->getTaxDepreciationForYear($year + 1);
        if ($nextYearDepreciation !== null && $nextYearDepreciation->isExecuted()) {
            return false;
        }

        return true;
    }
}
