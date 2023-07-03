<?php

namespace App\Entity;

use App\Majetek\Action\CreateEntityRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="accounting_entity")
 */
class AccountingEntity
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
     * @ORM\OneToMany(targetEntity="EntityUser", mappedBy="accountingEntity")
     */
    private Collection $entityUsers;
    /**
     * @ORM\Column(name="street", type="string", nullable=true)
     */
    private ?string $street;
    /**
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private ?string $city;
    /**
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private ?string $country;
    /**
     * @ORM\Column(name="zip_code", type="string", nullable=true)
     */
    private ?string $zipCode;
    /**
     * @ORM\Column(name="company_id", type="string", nullable=true)
     */
    private ?string $companyId;


    public function __construct(
        CreateEntityRequest $request,
    ){
        $this->name = $request->name;
        $this->companyId = $request->companyId;
        $this->country = $request->country;
        $this->city = $request->city;
        $this->zipCode = $request->zipCode;
        $this->street = $request->street;
        $this->entityUsers = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEntityUsers(): Collection
    {
        return $this->entityUsers;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getAddress(): string
    {
        $country = $this->getCountry();
        $city = $this->getCity();
        $zipCode = $this->getZipCode();
        $street = $this->getStreet();

        return $street . ', ' . $city . ' ' . $zipCode . ' ' . $country;
    }
}
