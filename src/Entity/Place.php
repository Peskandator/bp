<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="place")
 */
class Place
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
     * @ORM\Column(name="code", type="integer")
     */
    private int $code;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="places")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=false)
     */
    private Location $location;


    public function __construct(
        Location $location,
        string $name,
        int $code
    ){
        $this->location = $location;
        $this->name = $name;
        $this->code = $code;
    }

    public function update(string $name, int $code, Location $location): void
    {
        $this->name = $name;
        $this->code = $code;
        $this->location = $location;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
