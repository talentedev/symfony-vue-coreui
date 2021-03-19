<?php

namespace App\Service;

use App\Entity\Production;
use App\Repository\ProductionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductionService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * ProductionService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $productionId
     * @return Production
     */
    public function getProductionById(int $productionId): Production
    {
        /** @var Production $production */
        $production = $this->em->getRepository(Production::class)->find($productionId);
        return $production;
    }

    /**
     * @param \DateTime $date
     * @param int $productId
     * @return Production|null
     */
    public function getProductionByDate(\DateTime $date, int $productId): ?Production
    {
        /** @var ProductionRepository $productionRepository */
        $productionRepository = $this->em->getRepository(Production::class);
        /** @var Production $production */
        $production = $productionRepository->findByDate($date, $productId);
        return $production;
    }

    /**
     * @param int $productId
     * @param \DateTime $date
     * @return Production[]
     */
    public function findAllByDate(int $productId, \DateTime $date): array
    {
        /** @var ProductionRepository $productionRepository */
        $productionRepository = $this->em->getRepository(Production::class);
        /** @var Production[] $productions */
        $productions = $productionRepository->findAllByDate($productId, $date);
        return $productions;
    }

    /**
     * @return Production[]
     */
    public function findMaxDate(): array
    {
        /** @var ProductionRepository $productionRepository */
        $productionRepository = $this->em->getRepository(Production::class);
        /** @var Production[] $production */
        $production = $productionRepository->findMaxDate();
        return $production;
    }

    /**
     * @return Production[]
     */
    public function findMinDate(): array
    {
        /** @var ProductionRepository $productionRepository */
        $productionRepository = $this->em->getRepository(Production::class);
        /** @var Production[] $production */
        $production = $productionRepository->findMinDate();
        return $production;
    }
}
