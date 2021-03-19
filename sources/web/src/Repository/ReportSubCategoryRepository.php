<?php

namespace App\Repository;

use App\Entity\ReportSubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReportSubCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportSubCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportSubCategory[]    findAll()
 * @method ReportSubCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportSubCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReportSubCategory::class);
    }
}
