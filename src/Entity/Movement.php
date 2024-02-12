<?php

namespace App\Entity;

use App\Majetek\Enums\MovementType;
use App\Majetek\Requests\CreateMovementRequest;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="movement")
 */
class Movement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private int $type;
    /**
     * @ORM\Column(name="value", type="float", nullable=false)
     */
    private float $value;
    /**
     * @ORM\Column(name="residual_price", type="float", nullable=true)
     */
    private ?float $residualPrice;
    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private DateTimeInterface $date;
    /**
     * @ORM\Column(name="operation_date", type="date", nullable=false)
     */
    private DateTimeInterface $operationDate;
    /**
     * @ORM\Column(name="account_credited", type="string", nullable=true)
     */
    private ?string $accountCredited;
    /**
     * @ORM\Column(name="account_debited", type="string", nullable=true)
     */
    private ?string $accountDebited;
    /**
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private ?string $description;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset", inversedBy="movements")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id", nullable=false)
     */
    private Asset $asset;
    /**
     * @ORM\OneToOne(targetEntity="DepreciationTax")
     * @ORM\JoinColumn(name="depreciation_tax_id", referencedColumnName="id", nullable=true)
     */
    private ?DepreciationTax $depreciationTax;
    /**
     * @ORM\OneToOne(targetEntity="DepreciationAccounting")
     * @ORM\JoinColumn(name="depreciation_accounting_id", referencedColumnName="id", nullable=true)
     */
    private ?DepreciationAccounting $depreciationAccounting;


    public function __construct(
        CreateMovementRequest $request
    ){
        $this->update($request);
        $this->operationDate = new \DateTimeImmutable();
    }

    public function update(CreateMovementRequest $request): void
    {
        $this->asset = $request->asset;
        $this->type = $request->type;
        $this->value = $request->value;
        $this->residualPrice = $request->residualPrice;
        $this->date = $request->executionDate;
        $this->accountCredited = $request->accountCredited;
        $this->accountDebited = $request->accountDebited;
        $this->description = $request->description;
    }

    public function updateInclusionOrDisposal($value, $residualPrice, $date): void
    {
        $this->value = $value;
        $this->residualPrice = $residualPrice;
        $this->date = $date;
    }

    public function edit(string $description, ?DateTimeInterface $date, string $accDebited, string $accCredited) {
        $this->description = $description;
        $this->date = $date;
        $this->accountDebited = $accDebited;
        $this->accountCredited = $accCredited;
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

    public function getType(): int
    {
        return $this->type;
    }

    public function getTypeName(): string
    {
        return MovementType::NAMES[$this->type];
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getResidualPrice(): ?float
    {
        return $this->residualPrice;
    }

    public function setResidualPrice(?float $price): void
    {
        $this->residualPrice = $price;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getOperationDate(): DateTimeInterface
    {
        return $this->operationDate;
    }

    public function getAccountCredited(): string
    {
        return $this->accountCredited;
    }

    public function getAccountDebited(): string
    {
        return $this->accountDebited;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDepreciation(): ?Depreciation
    {
        if ($this->getType() === MovementType::DEPRECIATION_ACCOUNTING) {
            return $this->depreciationAccounting;
        }
        if ($this->getType() === MovementType::DEPRECIATION_TAX) {
            return $this->depreciationTax;
        }
        return null;
    }

    public function setTaxDepreciation(DepreciationTax $depreciationTax): void
    {
        $this->depreciationTax = $depreciationTax;
    }

    public function setAccountingDepreciation(DepreciationAccounting $depreciationAccounting): void
    {
        $this->depreciationAccounting = $depreciationAccounting;
    }

    public function isDeletable(): bool
    {
        $asset = $this->getAsset();

        if ($this->getType() === MovementType::DEPRECIATION_TAX) {
            $allMovements = $asset->getMovementsWithType(MovementType::DEPRECIATION_TAX);
            /**
             * @var Movement $movement
             */
            foreach ($allMovements as $movement) {
                $isPreviousYear = (int)$movement->getDate()->format('Y') < (int)$this->getDate()->format('Y');
                if ($isPreviousYear && $movement->getId() !== $this->getId()) {
                    return false;
                }
            }
        }
        if ($this->getType() === MovementType::DEPRECIATION_ACCOUNTING) {
            $allMovements = $asset->getMovementsWithType(MovementType::DEPRECIATION_ACCOUNTING);
            /**
             * @var Movement $movement
             */
            foreach ($allMovements as $movement) {
                $isPreviousYear = (int)$movement->getDate()->format('Y') < (int)$this->getDate()->format('Y');
                if ($isPreviousYear && $movement->getId() !== $this->getId()) {
                    return false;
                }
            }
        }
        if ($this->getType() === MovementType::INCLUSION) {
            $movements = $asset->getMovements();
            if ($movements->count() > 1) {
                return false;
            }
        }

        return true;
    }
}
