<?php

namespace App\Entity;

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
     * @ORM\Column(name="inclusion_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $inclusionDate;
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
     * @ORM\Column(name="disposal_price_tax", type="float", nullable=true)
     */
    private ?float $disposalPriceTax;
    /**
     * @ORM\Column(name="entry_price_accounting", type="float", nullable=true)
     */
    private ?float $entryPriceAccounting;
    /**
     * @ORM\Column(name="increased_entry_price_accounting", type="float", nullable=true)
     */
    private ?float $increasedEntryPriceAccounting;
    /**
     * @ORM\Column(name="disposal_price_accounting", type="float", nullable=true)
     */
    private ?float $disposalPriceAccounting;
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
     * @ORM\Column(name="variable_symbol", type="string", nullable=true)
     */
    private ?string $variableSymbol;
    /**
     * @ORM\Column(name="invoice_number", type="integer", nullable=true)
     */
    private ?int $invoiceNumber;
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
     * @ORM\JoinColumn(name="acquisition_id", referencedColumnName="id")
     */
    private Acquisition $acquisition;
    /**
     * @ORM\ManyToOne(targetEntity="Acquisition")
     * @ORM\JoinColumn(name="disposal_id", referencedColumnName="id")
     */
    private Acquisition $disposal;
    /**
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="acquisition_id", referencedColumnName="id", nullable=true)
     */
    private ?Place $place;


    public function __construct(
    ){
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

    public function getInclusionDate(): ?\DateTimeInterface
    {
        return $this->inclusionDate;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function getEntryPriceTax(): float
    {
        return $this->entryPriceTax;
    }

    public function getIncreasedEntryPriceTax(): float
    {
        return $this->increasedEntryPriceTax;
    }

    public function getDisposalPriceTax(): float
    {
        return $this->disposalPriceTax;
    }

    public function getEntryPriceAccounting(): float
    {
        return $this->entryPriceAccounting;
    }

    public function getIncresedEntryPriceAccounting(): float
    {
        return $this->increasedEntryPriceAccounting;
    }

    public function getDisposalPriceAccounting(): float
    {
        return $this->disposalPriceAccounting;
    }

    public function isDisposed(): bool
    {
        return $this->isDisposed;
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

    public function getInvoiceNumber(): ?int
    {
        return $this->invoiceNumber;
    }

    public function getVariableSymbol(): ?string
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
}
