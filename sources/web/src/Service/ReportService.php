<?php

namespace App\Service;

use App\Entity\Report;
use App\Entity\ReportCategory;
use App\Entity\ReportSubCategory;
use Doctrine\ORM\EntityManagerInterface;
use function Safe\substr;

class ReportService
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * ReportService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return ReportCategory[]
     */
    public function getReportCategories(): array
    {
        /** @var ReportCategory[] $categories */
        $categories = $this->em->getRepository(ReportCategory::class)->findAll();
        return $categories;
    }

    /**
     * @param mixed[] $categoryIds
     * @return ReportSubCategory[]
     */
    public function getReportSubCategories(array $categoryIds): array
    {
        /** @var ReportSubCategory[] $subCategories */
        $subCategories = $this->em->getRepository(ReportSubCategory::class)->findBy(array('reportCategories' => $categoryIds));
        return $subCategories;
    }

    /**
     * @param mixed[] $categoryIds
     * @param mixed[] $subCategoryIds
     * @param int $offset
     * @return Report[]
     */
    public function getReports(array $categoryIds, array $subCategoryIds, int $offset): array
    {
        $criteria = array();

        if ($categoryIds) {
            $criteria['reportCategory'] = $categoryIds;
        }
        if ($subCategoryIds) {
            $criteria['reportSubCategory'] = $subCategoryIds;
        }

        /** @var Report[] $reports */
        $reports = $this->em->getRepository(Report::class)->findBy($criteria);
        return $reports;
    }

    /**
     * @param int $categoryId
     * @return ReportCategory
     */
    public function getReportCategory(int $categoryId): ReportCategory
    {
        /** @var ReportCategory $reportCategory */
        $reportCategory = $this->em->getRepository(ReportCategory::class)->find($categoryId);
        return $reportCategory;
    }

    /**
     * @param int $subCategoryId
     * @return ReportSubCategory
     */
    public function getReportSubCategory(int $subCategoryId): ReportSubCategory
    {
        /** @var ReportSubCategory $reportSubCategory */
        $reportSubCategory = $this->em->getRepository(ReportSubCategory::class)->find($subCategoryId);
        return $reportSubCategory;
    }
}
