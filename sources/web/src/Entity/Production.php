<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductionRepository")
 * @ORM\Table(name="production")
 *
 */
class Production
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productions")
     */
    private $product;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $production;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $inventory;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return Production
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    /**
     * @param \DateTime $date
     * @return Production
     */
    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return Production
     */
    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInventory(): ?int
    {
        return $this->inventory;
    }

    /**
     * @param int $inventory
     * @return Production
     */
    public function setInventory(int $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getProduction(): ?int
    {
        return $this->production;
    }

    /**
     * @param int $production
     * @return Production
     */
    public function setProduction(int $production): self
    {
        $this->production = $production;

        return $this;
    }
}
