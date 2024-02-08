<?php

namespace App\Entity;

use App\Majetek\Enums\MovementType;
use App\Majetek\Requests\CreateAssetRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="asset")
 */
class Asset
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
    private string $name;
    /**
     * @ORM\Column(name="inventory_number", type="integer")
     */
    private int $inventoryNumber;
    /**
     * @ORM\Column(name="entry_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $entryDate;
    /**
     * @ORM\Column(name="acquisition_date", type="date", nullable=false)
     */
    private \DateTimeInterface $acquisitionDate;
    /**
     * @ORM\Column(name="entry_price", type="float", nullable=true)
     */
    private ?float $entryPrice;
    /**
     * @ORM\Column(name="increased_entry_price", type="float", nullable=true)
     */
    private ?float $increasedEntryPrice;
    /**
     * @ORM\Column(name="increase_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $increaseDate;
    /**
     * @ORM\Column(name="depreciated_amount_tax", type="float", nullable=true)
     */
    private ?float $depreciatedAmountTax;
    /**
     * @ORM\Column(name="depreciated_amount_accounting", type="float", nullable=true)
     */
    private ?float $depreciatedAmountAccounting;
    /**
     * @ORM\Column(name="is_disposed", type="boolean")
     */
    private bool $isDisposed;
    /**
     * @ORM\Column(name="only_tax", type="boolean")
     */
    private bool $isOnlyTax;
    /**
     * @ORM\Column(name="has_tax_depreciations", type="boolean")
     */
    private bool $hasTaxDepreciations;
    /**
     * @ORM\Column(name="is_included", type="boolean")
     */
    private bool $isIncluded;
    /**
     * @ORM\Column(name="producer", type="string", nullable=true)
     */
    private ?string $producer;
    /**
     * @ORM\Column(name="disposal_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $disposalDate;
    /**
     * @ORM\Column(name="variable_symbol", type="integer", nullable=true)
     */
    private ?int $variableSymbol;
    /**
     * @ORM\Column(name="invoice_number", type="string", nullable=true)
     */
    private ?string $invoiceNumber;
    /**
     * @ORM\Column(name="units", type="integer", nullable=true)
     */
    private ?int $units;
    /**
     * @ORM\Column(name="depreciation_year_tax", type="integer", nullable=true)
     */
    private ?int $depreciationYearTax;
    /**
     * @ORM\Column(name="depreciation_year_accounting", type="integer", nullable=true)
     */
    private ?int $depreciationYearAccounting;
    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private ?string $note;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="assets")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;
    /**
     * @ORM\ManyToOne(targetEntity="AssetType")
     * @ORM\JoinColumn(name="asset_type_id", referencedColumnName="id")
     */
    private AssetType $assetType;
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private ?Category $category;
    /**
     * @ORM\ManyToOne(targetEntity="Acquisition")
     * @ORM\JoinColumn(name="acquisition_id", referencedColumnName="id", nullable=true)
     */
    private ?Acquisition $acquisition;
    /**
     * @ORM\ManyToOne(targetEntity="Disposal")
     * @ORM\JoinColumn(name="disposal_id", referencedColumnName="id", nullable=true)
     */
    private ?Disposal $disposal;
    /**
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="id", nullable=true)
     */
    private ?Place $place;
    /**
     * @ORM\ManyToOne(targetEntity="DepreciationGroup")
     * @ORM\JoinColumn(name="depreciation_group_tax", referencedColumnName="id", nullable=true)
     */
    private ?DepreciationGroup $depreciationGroupTax;
    /**
     * @ORM\ManyToOne(targetEntity="DepreciationGroup")
     * @ORM\JoinColumn(name="depreciation_group_accounting", referencedColumnName="id", nullable=true)
     */
    private ?DepreciationGroup $depreciationGroupAccounting;
    /**
     * @ORM\OneToMany(targetEntity="DepreciationTax", mappedBy="asset")
     */
    private Collection $depreciationsTax;
    /**
     * @ORM\OneToMany(targetEntity="DepreciationAccounting", mappedBy="asset")
     */
    private Collection $depreciationsAccounting;
    /**
     * @ORM\OneToMany(targetEntity="Movement", mappedBy="asset")
     */
    private Collection $movements;

    public function __construct(
        AccountingEntity $entity,
        CreateAssetRequest $request,
    )
    {
        $this->updateFromRequest($request);
        $this->entity = $entity;
        $this->isDisposed = false;
        $this->note = $request->note;
        $this->depreciationsTax = new ArrayCollection();
        $this->depreciationsAccounting = new ArrayCollection();
        $this->movements = new ArrayCollection();
        $this->acquisitionDate = new \DateTimeImmutable();
    }

    public function update(CreateAssetRequest $request): void
    {
        $this->updateFromRequest($request);
        $this->isDisposed = false;
        $this->note = $request->note;
    }

    protected function updateFromRequest(CreateAssetRequest $request)
    {
        $this->assetType = $request->type;
        $this->name = $request->name;
        $this->inventoryNumber = $request->inventoryNumber;
        $this->producer = $request->producer;
        $this->category = $request->category;
        $this->acquisition = $request->acquisition;
        $this->disposal = $request->disposal;
        $this->place = $request->place;
        $this->units = $request->units;
        $this->isOnlyTax = $request->onlyTax;
        $this->hasTaxDepreciations = $request->hasTaxDepreciations;
        $this->isIncluded = $request->isIncluded;
        $this->depreciationGroupTax = $request->depreciationGroupTax;
        $this->entryPrice = $request->entryPrice;
        $this->increasedEntryPrice = $request->increasedEntryPrice;
        $this->increaseDate = $request->increaseDate;
        $this->depreciatedAmountTax = $request->depreciatedAmountTax;
        $this->depreciationYearTax = $request->depreciationYearTax;
        $this->depreciationGroupAccounting = $request->depreciationGroupAccounting;
        $this->depreciatedAmountAccounting = $request->depreciatedAmountAccounting;
        $this->depreciationYearAccounting = $request->depreciationYearAccounting;
        $this->invoiceNumber = $request->invoiceNumber;
        $this->variableSymbol = $request->variableSymbol;
        $this->entryDate = $request->entryDate;
        $this->disposalDate = $request->disposalDate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getInventoryNumber(): int
    {
        return $this->inventoryNumber;
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function getAcquisitionDate(): ?\DateTimeInterface
    {
        return $this->acquisitionDate;
    }

    public function getAcquisitionYear(): int
    {
        $acquisitionDate = $this->getAcquisitionDate();
        return (int)$acquisitionDate->format('Y');
    }

    public function getAcquisitionMonth(): int
    {
        $acquisitionDate = $this->getAcquisitionDate();
        return (int)$acquisitionDate->format('m');
    }

    public function getEntryPrice(): ?float
    {
        return $this->entryPrice;
    }

    public function getIncreasedEntryPrice(): ?float
    {
        return $this->increasedEntryPrice;
    }

    public function getCorrectEntryPrice(): ?float
    {
        $increasedPrice = $this->getIncreasedEntryPrice();
        $entryPrice = $this->getEntryPrice();
        if ($increasedPrice !== null) {
            return $increasedPrice;
        }

        return $entryPrice;
    }

    public function recalculateIncreasedEntryPrice(): void
    {
        $movements = $this->getMovementsWithType(MovementType::ENTRY_PRICE_CHANGE);
        $entryPrice = $this->getEntryPrice();

        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            $entryPrice += $movement->getValue();
        }
        $this->entryPrice = $entryPrice;
    }

    public function getIncreaseDate(): ?\DateTimeInterface
    {
        return $this->increaseDate;
    }

    public function setIncreaseDate(\DateTimeInterface $date): void
    {
         $this->increaseDate = $date;
    }

    public function getBaseDepreciatedAmountTax(): ?float
    {
        return $this->depreciatedAmountTax;
    }

    public function getBaseDepreciatedAmountAccounting(): ?float
    {
        return $this->depreciatedAmountAccounting;
    }

    public function getDepreciatedAmountTax(): ?float
    {
        return $this->depreciatedAmountTax + $this->getExecutedTaxDepreciationsAmount();
    }

    public function getDepreciatedAmountAccounting(): ?float
    {
        return $this->depreciatedAmountAccounting + $this->getExecutedAccountingDepreciationsAmount();
    }

    public function isDisposed(): bool
    {
        return $this->isDisposed;
    }

    public function getDepreciationYearTax(): ?int
    {
        return $this->depreciationYearTax;
    }

    public function getDepreciationYearAccounting(): ?int
    {
        return $this->depreciationYearAccounting;
    }

    public function getExecutedTaxDepreciationsAmount(): float
    {
        $sum = 0;
        $depreciations = $this->getExecutedTaxDepreciations();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($depreciations as $depreciation) {
            $sum += $depreciation->getDepreciationAmount();
        }

        return $sum;
    }

    public function getExecutedAccountingDepreciationsAmount(): float
    {
        $sum = 0;
        $depreciations = $this->getExecutedAccountingDepreciations();
        /**
         * @var DepreciationAccounting $depreciation
         */
        foreach ($depreciations as $depreciation) {
            $sum += $depreciation->getDepreciationAmount();
        }

        return $sum;
    }

    public function isOnlyTax(): bool
    {
        return $this->isOnlyTax;
    }

    public function hasTaxDepreciations(): bool
    {
        return $this->hasTaxDepreciations;
    }

    public function isIncluded(): bool
    {
        return $this->isIncluded;
    }

    public function setIsIncluded(bool $isIncluded): void
    {
        $this->isIncluded = $isIncluded;
    }

    public function setDisposalDate(?\DateTimeInterface $disposalDate): void
    {
        $this->disposalDate = $disposalDate;
    }

    public function getDisposalDate(): ?\DateTimeInterface
    {
        return $this->disposalDate;
    }

    public function getDisposalYear(): ?int
    {
        $disposalDate = $this->getDisposalDate();
        if ($disposalDate !== null) {
            return (int)$disposalDate->format('Y');
        }
        return null;
    }

    public function getDisposalMonth(): ?int
    {
        $disposalDate = $this->getDisposalDate();
        if ($disposalDate !== null) {
            return (int)$disposalDate->format('m');
        }
        return null;
    }

    public function getProducer(): ?string
    {
        return $this->producer;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function getVariableSymbol(): ?int
    {
        return $this->variableSymbol;
    }

    public function getAssetType(): AssetType
    {
        return $this->assetType;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function getLocation(): ?Location
    {
        $place = $this->getPlace();
        if ($place) {
            return $place->getLocation();
        }
        return null;
    }

    public function getAcquisition(): ?Acquisition
    {
        return $this->acquisition;
    }

    public function getDisposal(): ?Disposal
    {
        return $this->disposal;
    }

    public function getDepreciationGroupTax(): ?DepreciationGroup
    {
        return $this->depreciationGroupTax;
    }
    public function getDepreciationGroupAccounting(): ?DepreciationGroup
    {
        return $this->depreciationGroupAccounting;
    }

    public function getAmortisedPriceTax(): ?float
    {
        return $this->getCorrectEntryPrice() - $this->getDepreciatedAmountTax();
    }

    public function getAmortisedPriceAccounting(): ?float
    {
        return $this->getCorrectEntryPrice() - $this->getDepreciatedAmountAccounting();
    }

    public function getUnits(): ?int
    {
        return $this->units;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function isWithTaxDepreciations(): bool
    {
        $typeCode = $this->getAssetType()->getCode();
        if ($typeCode === 1 && $this->hasTaxDepreciations() && $this->isIncluded()) {
            return true;
        }

        return false;
    }

    public function hasAccountingDepreciations(): bool
    {
        $typeCode = $this->getAssetType()->getCode();
        if ($typeCode === 1 && $this->isIncluded()) {
            return true;
        }
        if ($typeCode === 3 && !$this->isOnlyTax() && $this->isIncluded()) {
            return true;
        }

        return false;
    }

    public function getTaxDepreciations(): Collection
    {
        return $this->depreciationsTax;
    }

    public function getAccountingDepreciations(): Collection
    {
        return $this->depreciationsAccounting;
    }

    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function getMovementsWithType(int $type): array
    {
        $resultArr = [];
        /**
         * @var Movement $movement
         */
        foreach ($this->getMovements() as $movement) {
            if ($movement->getType() === $type) {
                $resultArr[] = $movement;
            }
        }

        return $resultArr;
    }

    public function getInclusionMovement(): ?Movement
    {
        $movements = $this->getMovementsWithType(MovementType::INCLUSION);
        if (isset($movements[0])) {
            return $movements[0];
        }

        return null;
    }

    public function getDisposalMovement(): ?Movement
    {
        $movements = $this->getMovementsWithType(MovementType::DISPOSAL);
        if (isset($movements[0])) {
            return $movements[0];
        }

        return null;
    }

    public function getDepreciationTaxExecutionMovement(DepreciationTax $depreciationTax): ?Movement
    {
        $depreciationTaxId = $depreciationTax->getId();
        $movements = $this->getMovementsWithType(MovementType::DEPRECIATION_TAX);
        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            if ($movement->getDepreciation()->getId() === $depreciationTaxId) {
                return $movement;
            }
        }

        return null;
    }

    public function getDepreciationAccountingExecutionMovement(DepreciationAccounting $depreciationAccounting): ?Movement
    {
        $depreciationAccountingId = $depreciationAccounting->getId();
        $movements = $this->getMovementsWithType(MovementType::DEPRECIATION_ACCOUNTING);
        /**
         * @var Movement $movement
         */
        foreach ($movements as $movement) {
            if ($movement->getDepreciation()->getId() === $depreciationAccountingId) {
                return $movement;
            }
        }

        return null;
    }

    public function clearTaxDepreciations(): void
    {
        $this->depreciationsTax->clear();
    }

    public function clearAccountingDepreciations(): void
    {
        $this->depreciationsAccounting->clear();
    }

    public function clearMovements(): void
    {
        $this->movements->clear();
    }

    public function getExecutedTaxDepreciations(): Collection
    {
        $executedDepreciations = new ArrayCollection();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($this->getTaxDepreciations() as $depreciation) {
            if ($depreciation->isExecuted()) {
                $executedDepreciations->add($depreciation);
            }
        }
        return $executedDepreciations;
    }

    public function getExecutedAccountingDepreciations(): Collection
    {
        $executedDepreciations = new ArrayCollection();
        /**
         * @var DepreciationAccounting $depreciation
         */
        foreach ($this->getAccountingDepreciations() as $depreciation) {
            if ($depreciation->isExecuted()) {
                $executedDepreciations->add($depreciation);
            }
        }
        return $executedDepreciations;
    }

    public function hasExecutedDepreciations(): bool
    {
        if ($this->getExecutedTaxDepreciations()->count() > 0 || $this->getExecutedAccountingDepreciations()->count() > 0) {
            return true;
        }
        return false;
    }

    public function addTaxDepreciation(DepreciationTax $depreciation): void
    {
        $depreciations = $this->getTaxDepreciations();
        $depreciations->add($depreciation);
    }

    public function addAccountingDepreciation(DepreciationAccounting $depreciation): void
    {
        $depreciations = $this->getAccountingDepreciations();
        $depreciations->add($depreciation);
    }

    public function getTaxDepreciationForDepreciationYear(int $depreciationYear): ?DepreciationTax
    {
        $taxDepreciations = $this->getTaxDepreciations();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($taxDepreciations as $depreciation) {
            if ($depreciation->getDepreciationYear() === $depreciationYear && $depreciation->isExecutable()) {
                return $depreciation;
            }
        }
        return null;
    }

    public function getAccountingDepreciationForDepreciationYear(int $depreciationYear): ?DepreciationAccounting
    {
        $accountingDepreciations = $this->getAccountingDepreciations();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($accountingDepreciations as $depreciation) {
            if ($depreciation->getDepreciationYear() === $depreciationYear && $depreciation->isExecutable()) {
                return $depreciation;
            }
        }
        return null;
    }

    public function getTaxDepreciationForYear(int $year): ?DepreciationTax
    {
        $taxDepreciations = $this->getTaxDepreciations();
        /**
         * @var DepreciationTax $depreciation
         */
        foreach ($taxDepreciations as $depreciation) {
            if ($depreciation->getYear() === $year) {
                return $depreciation;
            }
        }
        return null;
    }

    public function getAccountingDepreciationForYear(int $year): ?DepreciationAccounting
    {
        $accountingDepreciations = $this->getAccountingDepreciations();
        /**
         * @var DepreciationAccounting $depreciation
         */
        foreach ($accountingDepreciations as $depreciation) {
            if ($depreciation->getYear() === $year) {
                return $depreciation;
            }
        }
        return null;
    }
}
