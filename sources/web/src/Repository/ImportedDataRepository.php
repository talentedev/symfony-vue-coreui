<?php

namespace App\Repository;

use App\Entity\ImportedData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImportedData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportedData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportedData[]    findAll()
 * @method ImportedData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportedDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImportedData::class);
    }

    /**
     * @param mixed[] $commodites
     * @param mixed[] $types
     * @param mixed[] $countries
     * @param \DateTime $from
     * @param \DateTime $to
     * @return ImportedData[]
     */
    public function getResult(array $commodites, array $types, array $countries, \DateTime $from, \DateTime $to): array
    {
        $from->modify('first day of this month');
        $from->modify('noon');
        $to->modify('last day of this month');
        $to->modify('noon');

        $qb = $this->createQueryBuilder("e");
        $qb->select("e.commodity, e.type, e.country, GROUP_CONCAT(DISTINCT CONCAT(e.date,'=',e.value) SEPARATOR ';') AS data")
            ->where('e.date BETWEEN :from AND :to')
            ->groupBy('e.commodity, e.type, e.country')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;

        if ($commodites) {
            $qb->andWhere('e.commodity IN (:commodites)')
                ->setParameter('commodites', $commodites);
        }
        if ($types) {
            $qb->andWhere('e.type IN (:types)')
                ->setParameter('types', $types);
        }
        if ($countries) {
            $qb->andWhere('e.country IN (:countries)')
                ->setParameter('countries', $countries);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return ImportedData[]
     */
    public function getCommoditiesTypes(): array
    {
        $qb = $this->createQueryBuilder("e");
        $qb->select("e.commodity, e.type")
            ->groupBy('e.commodity, e.type')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return ImportedData[]
     */
    public function getCountryRegionPairs(): array
    {
        $qb = $this->createQueryBuilder("e");
        $qb->select("e.region, e.country")
            ->groupBy('e.region, e.country')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
