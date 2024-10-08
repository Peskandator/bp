<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

/**
 * @ORM\Entity()
 * @ORM\Table(name="depreciations_accounting_data")
 */
class DepreciationsAccountingData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    private int $year;
    /**
     * @ORM\Column(name="code", type="string", nullable=false)
     */
    private string $code;
    /**
     * @ORM\Column(name="origin", type="string", nullable=true)
     */
    private ?string $origin;
    /**
     * @ORM\Column(name="document", type="integer", nullable=true)
     */
    private ?int $document;
    /**
     * @ORM\Column(name="operation_month", type="integer", nullable=true)
     */
    private ?int $operationMonth;
    /**
     * @ORM\Column(name="operation_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $operationDate;
    /**
     * @ORM\Column(name="data", type="json", nullable=false)
     */
    private string $data;
    /**
     * @ORM\Column(name="updated_at", type="date", nullable=false)
     */
    private \DateTimeInterface $updatedAt;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="assets")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;


    public function __construct(
        $entity,
        $year,
        $code,
        $data
    ){
        $this->entity = $entity;
        $this->year = $year;
        $this->code = $code;
        $this->data = $data;
        $this->origin = 'O-IM';
        $this->document = 0;
        $this->operationMonth = 12;

        $defaultDate = $year . '-12-31';
        $date = new DateTime($defaultDate);
        $this->operationDate = $date;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function update($data, string $origin, int $document, int $operationMonth, \DateTimeInterface $operationDate) {
        $this->data = $data;
        $this->origin = $origin;
        $this->document = $document;
        $this->operationMonth = $operationMonth;
        $this->operationDate = $operationDate;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getArrayData(): array
    {
        return Json::decode($this->getData(), Json::FORCE_ARRAY);
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function setDataArray(array $data): void
    {
        $this->data = Json::encode($data);
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function getDocument(): ?int
    {
        return $this->document;
    }

    public function getOperationMonth(): ?int
    {
        return $this->operationMonth;
    }

    public function getOperationDate(): ?\DateTimeInterface
    {
        return $this->operationDate;
    }
}
