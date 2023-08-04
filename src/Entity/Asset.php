<?php

namespace App\Entity;

use App\Majetek\Requests\CreateAssetRequest;
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
     * @ORM\Column(name="entry_price_tax", type="float", nullable=true)
     */
    private ?float $entryPriceTax;
    /**
     * @ORM\Column(name="increased_entry_price_tax", type="float", nullable=true)
     */
    private ?float $increasedEntryPriceTax;
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
     * @ORM\Column(name="depreciation_increased_year_tax", type="integer", nullable=true)
     */
    private ?int $depreciationIncreasedYearTax;
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
     * @ORM\ManyToOne(targetEntity="Acquisition")
     * @ORM\JoinColumn(name="disposal_id", referencedColumnName="id", nullable=true)
     */
    private ?Acquisition $disposal;
    /**
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="acquisition_id", referencedColumnName="id", nullable=true)
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


    public function __construct(
        AccountingEntity $entity,
        CreateAssetRequest $request
    )
    {
        $this->entity = $entity;
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
        $this->depreciationGroupTax = $request->depreciationGroupTax;
        $this->entryPriceTax = $request->entryPriceTax;
        $this->increasedEntryPriceTax = $request->increasedPriceTax;
        $this->depreciatedAmountTax = $request->depreciatedAmountTax;
        $this->depreciationYearTax = $request->depreciationYearTax;
        $this->depreciationIncreasedYearTax = $request->depreciationIncreasedYearTax;
        $this->depreciationGroupAccounting = $request->depreciationGroupAccounting;
        $this->entryPriceAccounting = $request->entryPriceAccounting;
        $this->increasedEntryPriceAccounting = $request->increasedPriceAccounting;
        $this->depreciatedAmountAccounting = $request->depreciatedAmountAccounting;
        $this->invoiceNumber = $request->invoiceNumber;
        $this->variableSymbol = $request->variableSymbol;
        $this->entryDate = $request->entryDate;
        $this->disposalDate = $request->disposalDate;
        $this->isDisposed = false;
    }

    public function update(): void
    {
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

    public function getEntryPriceTax(): ?float
    {
        return $this->entryPriceTax;
    }

    public function getIncreasedEntryPriceTax(): ?float
    {
        return $this->increasedEntryPriceTax;
    }

    public function getDepreciatedAmountTax(): ?float
    {
        return $this->depreciatedAmountTax;
    }

    public function getEntryPriceAccounting(): ?float
    {
        return $this->entryPriceAccounting;
    }

    public function getIncresedEntryPriceAccounting(): ?float
    {
        return $this->increasedEntryPriceAccounting;
    }

    public function getDepreciatedAmountAccounting(): ?float
    {
        return $this->depreciatedAmountAccounting;
    }

    public function isDisposed(): bool
    {
        return $this->isDisposed;
    }

    public function getDepreciationYearTax(): ?int
    {
        return $this->depreciationYearTax;
    }

    public function getDepreciationIncreasedYearTax(): ?int
    {
        return $this->depreciationIncreasedYearTax;
    }

    public function isOnlyTax(): bool
    {
        return $this->isOnlyTax;
    }

    public function getDisposalDate(): ?\DateTimeInterface
    {
        return $this->disposalDate;
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

    public function getDisposal(): ?Acquisition
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
}
