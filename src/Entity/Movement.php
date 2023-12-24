<?php

namespace App\Entity;

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
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private DateTimeInterface $date;
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


    public function __construct(
        CreateMovementRequest $request
    ){
        $this->asset = $request->asset;
        $this->type = $request->type;
        $this->value = $request->value;
        $this->date = $request->date;
        $this->accountCredited = $request->accountCredited;
        $this->accountDebited = $request->accountDebited;
        $this->description = $request->description;
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
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
}
