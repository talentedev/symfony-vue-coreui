<?php

namespace App\Service;

use App\Entity\Commodity;
use Doctrine\ORM\EntityManagerInterface;

class CommodityService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * CommodityService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Commodity[]
     */
    public function getAll(): array
    {
        /** @var Commodity[] $commodities */
        $commodities = $this->em->getRepository(Commodity::class)->findAll();
        return $commodities;
    }

    /**
     * @param int $commodityId
     * @return Commodity
     */
    public function getCommodityById(int $commodityId): Commodity
    {
        /** @var Commodity $commodity */
        $commodity = $this->em->getRepository(Commodity::class)->find($commodityId);
        return $commodity;
    }
}
