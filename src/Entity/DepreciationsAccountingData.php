<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(name="data", type="json", nullable=false)
     */
    private string $data;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccountingEntity", inversedBy="assets")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private AccountingEntity $entity;


    public function __construct(
        $entity,
        $year,
        $data
    ){
        $this->entity = $entity;
        $this->year = $year;
        $this->data = $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getData(): string
    {
        return $this->year;
    }

    public function getArrayData(): array
    {
        return Json::decode($this->getData(), Json::FORCE_ARRAY);
    }

    public function getEntity(): AccountingEntity
    {
        return $this->entity;
    }
}
