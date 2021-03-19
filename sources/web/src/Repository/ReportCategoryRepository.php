<?php

namespace App\Repository;

use App\Entity\ReportCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReportCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportCategory[]    findAll()
 * @method ReportCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReportCategory::class);
    }
}
