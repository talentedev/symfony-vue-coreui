<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="commodity")
 *
 */
class Commodity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CommodityGroup", inversedBy="commodities")
     */
    private $commodityGroup;

    private $products;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Commodity
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return CommodityGroup
     */
    public function getCommodityGroup(): ?CommodityGroup
    {
        return $this->commodityGroup;
    }

    /**
     * @param CommodityGroup $commodityGroup
     * @return Commodity
     */
    public function setCommodityGroup(?CommodityGroup $commodityGroup): self
    {
        $this->commodityGroup = $commodityGroup;

        return $this;
    }
}
