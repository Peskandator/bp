<?php

namespace App\Entity;

use App\Majetek\Enums\DepreciationMethod;
use App\Majetek\Requests\CreateEntityRequest;
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
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="entity")
     */
    private Collection $locations;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Acquisition", mappedBy="entity")
     */
    private Collection $acquisitions;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DepreciationGroup", mappedBy="entity")
     */
    private Collection $depreciationGroups;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="entity")
     */
    private Collection $categories;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AssetType", mappedBy="entity")
     */
    private Collection $assetTypes;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Asset", mappedBy="entity")
     */
    private Collection $assets;

    public function __construct(
        CreateEntityRequest $request,
    ){

        $this->update($request);
        $this->entityUsers = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->acquisitions = new ArrayCollection();
        $this->assetTypes = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->depreciationGroups = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function update(CreateEntityRequest $request)
    {
        $this->name = $request->name;
        $this->companyId = $request->companyId;
        $this->country = $request->country;
        $this->city = $request->city;
        $this->zipCode = $request->zipCode;
        $this->street = $request->street;
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

        return $street . ', ' . $city . ' ' . $zipCode . ', ' . $country;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function getAcquisitionsAndDisposals(): Collection
    {
        return $this->acquisitions;
    }


    public function getAcquisitions(): Collection
    {
        $returnArr = [];
        $acquisitions = $this->acquisitions;

        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            if (!$acquisition->isDisposal()) {
                $returnArr[] = $acquisition;
            }
        }

        return new ArrayCollection($returnArr);
    }

    public function getDisposals(): Collection
    {
        $returnArr = [];
        $acquisitions = $this->acquisitions;

        /**
         * @var Acquisition $acquisition
         */
        foreach ($acquisitions as $acquisition) {
            if ($acquisition->isDisposal()) {
                $returnArr[] = $acquisition;
            }
        }

        return new ArrayCollection($returnArr);
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }


    public function getDepreciationGroups(): Collection
    {
        return $this->depreciationGroups;
    }

    public function getDepreciationGroupsWithoutAccounting(): Collection
    {
        $groups = $this->depreciationGroups;
        $groupsWithoutAccounting = [];
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            if ($group->getMethod() === DepreciationMethod::ACCOUNTING) {
                continue;
            }
            $groupsWithoutAccounting[] = $group;
        }

        return new ArrayCollection($groupsWithoutAccounting);
    }

    public function getAccountingDepreciationGroups(): Collection
    {
        $groups = $this->depreciationGroups;
        $accountingGroups = [];
        /**
         * @var DepreciationGroup $group
         */
        foreach ($groups as $group) {
            if ($group->getMethod() === DepreciationMethod::ACCOUNTING) {
                $accountingGroups[] = $group;
            }
        }

        return new ArrayCollection($accountingGroups);
    }


    public function getAssetTypes(): Collection
    {
        return $this->assetTypes;
    }

    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function getPlaces(): array
    {
        $places = [];

        $locations = $this->getLocations();

        /**
         * @var Location $location
         */
        foreach ($locations as $location) {
            $locationPlaces = $location->getPlaces();
            /**
             * @var Place $locationPlace
             */
            foreach ($locationPlaces as $locationPlace) {
                $places[] = $locationPlace;
            }
        }

        return $places;
    }

    public function isEntityUser(User $user): bool
    {
        $entityUser = $user->getEntityUser($this);
        if ($entityUser !== null) {
            return true;
        }

        return false;
    }
}
