<?php

namespace App\Entity;

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
     * @ORM\Column(name="entry_price_tax", type="float", nullable=true)
     */
    private ?float $entryPriceTax;
    /**
     * @ORM\Column(name="increased_entry_price_tax", type="float", nullable=true)
     */
    private ?float $increasedEntryPriceTax;
    /**
     * @ORM\Column(name="increase_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $increaseDate;
    /**
     * @ORM\Column(name="depreciated_amount_tax", type="float", nullable=true)
     */
    private ?float $depreciatedAmountTax;
    /**
     * @ORM\Column(name="entry_price_accounting", type="float", nullable=true)
     */
    private ?float $entryPriceAccounting;
    /**
     * @ORM\Column(name="increased_entry_price_accounting", type="float", nullable=true)
     */
    private ?float $increasedEntryPriceAccounting;
    /**
     * @ORM\Column(name="increase_date_accounting", type="date", nullable=true)
     */
    private ?\DateTimeInterface $increaseDateAccounting;
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
        CreateAssetRequest $request
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
        $this->entryPriceTax = $request->entryPriceTax;
        $this->increasedEntryPriceTax = $request->increasedPriceTax;
        $this->increaseDate = $request->increaseDate;
        $this->depreciatedAmountTax = $request->depreciatedAmountTax;
        $this->depreciationYearTax = $request->depreciationYearTax;
        $this->depreciationGroupAccounting = $request->depreciationGroupAccounting;
        $this->entryPriceAccounting = $request->entryPriceAccounting;
        $this->increasedEntryPriceAccounting = $request->increasedPriceAccounting;
        $this->increaseDateAccounting = $request->increaseDateAccounting;
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

    public function getEntryPriceTax(): ?float
    {
        return $this->entryPriceTax;
    }

    public function getIncreasedEntryPriceTax(): ?float
    {
        return $this->increasedEntryPriceTax;
    }

    public function getCorrectEntryPriceTax(): ?float
    {
        $increasedPrice = $this->increasedEntryPriceTax;
        $entryPrice = $this->entryPriceTax;
        if ($increasedPrice !== null) {
            return $increasedPrice;
        }

        return $entryPrice;
    }

    public function getIncreaseDateTax(): ?\DateTimeInterface
    {
        return $this->increaseDate;
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

    public function getEntryPriceAccounting(): ?float
    {
        return $this->entryPriceAccounting;
    }

    public function getIncreasedEntryPriceAccounting(): ?float
    {
        return $this->increasedEntryPriceAccounting;
    }

    public function getCorrectEntryPriceAccounting(): ?float
    {
        $increasedPrice = $this->increasedEntryPriceAccounting;
        $entryPrice = $this->entryPriceAccounting;
        if ($increasedPrice !== null) {
            return $increasedPrice;
        }

        return $entryPrice;
    }

    public function getIncreaseDateAccounting(): ?\DateTimeInterface
    {
        return $this->increaseDateAccounting;
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

    public function getTotalDepreciationsTax(): ?float
    {
        return 0;
    }

    public function getTotalDepreciationsAccounting(): ?float
    {
        return 0;
    }

    public function getAmortisedPriceTax(): ?float
    {
        return $this->getEntryPriceTax() - $this->getTotalDepreciationsTax();
    }

    public function getAmortisedPriceAccounting(): ?float
    {
        return $this->getEntryPriceAccounting() - $this->getTotalDepreciationsAccounting();
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
        if ($typeCode === 1 && $this->hasTaxDepreciations()) {
            return true;
        }

        return false;
    }

    public function hasAccountingDepreciations(): bool
    {
        $typeCode = $this->getAssetType()->getCode();
        if ($typeCode === 1) {
            return true;
        }
        if ($typeCode === 3 && !$this->isOnlyTax()) {
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
