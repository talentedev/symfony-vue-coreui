<?php

namespace App\Repository;

use App\Entity\Production;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Production|null find($id, $lockMode = null, $lockVersion = null)
 * @method Production|null findOneBy(array $criteria, array $orderBy = null)
 * @method Production[]    findAll()
 * @method Production[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Production::class);
    }

    /**
     * @param \DateTime $date
     * @param int $productId
     * @return Production|null
     */
    public function findByDate(\DateTime $date, int $productId): ?Production
    {
        $month = $date->format('m');

        $from = new \DateTime($date->format("Y") . "-" . $month . "-01 00:00:00");
        $to   = new \DateTime($date->format("Y") . "-" . $month . "-28 23:59:59");

        $qb = $this->createQueryBuilder("e");
        $qb->where('e.product = :product')
            ->andWhere('e.date BETWEEN :from AND :to')
            ->setParameter('product', $productId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * @param int $productId
     * @param \DateTime $date
     * @return Production[]
     */
    public function findAllByDate(int $productId, \DateTime $date): array
    {
        $month = $date->format('m');
        $year = $date->format('Y');

        $from = null;
        $to = null;

        if (intval($month) > 6) {
            $from = new \DateTime($year . "-" . (intval($month) - 6) . "-01 00:00:00");
            $to   = new \DateTime($year . "-" . intval($month) . "-28 23:59:59");
        } else {
            $lastYear = intval($year) - 1;
            $lastMonth = intval($month) + 6;
            $from = new \DateTime($lastYear . "-" . $lastMonth . "-01 00:00:00");
            $to   = new \DateTime($year . "-" . intval($month) . "-28 23:59:59");
        }

        $qb = $this->createQueryBuilder("e");
        $qb->where('e.product = :product')
            ->andWhere('e.date BETWEEN :from AND :to')
            ->setParameter('product', $productId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return Production[]
     */
    public function findMaxDate(): array
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select($qb->expr()->max('p.date'))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Production[]
     */
    public function findMinDate(): array
    {
        $qb = $this->createQueryBuilder("p");
        $qb->select($qb->expr()->min('p.date'))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
