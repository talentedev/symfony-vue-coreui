<?php

namespace App\Service;

use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;

class RegionService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * RegionService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Region[]
     */
    public function getAll(): array
    {
        /** @var Region[] $regions */
        $regions = $this->em->getRepository(Region::class)->findAll();
        return $regions;
    }
}
