<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 * @ORM\Table(name="report")
 */
class Report
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
     * @ORM\Column(type="datetime")
     */
    private $uploadDate;

    /**
     * @ORM\Column(type="string")
     */
    private $file;

    /**
     * @var ReportCategory
     * @ORM\ManyToOne(targetEntity="ReportCategory", inversedBy="reports")
     * @ORM\JoinColumn(name="report_category_id", referencedColumnName="id", nullable=false)
     */
    private $reportCategory;

    /**
     * @var ReportSubCategory
     * @ORM\ManyToOne(targetEntity="ReportSubCategory", inversedBy="reports")
     * @ORM\JoinColumn(name="report_sub_category_id", referencedColumnName="id", nullable=false)
     */
    private $reportSubCategory;

    /**
     * Report constructor.
     * @param ReportCategory $reportCategory
     * @param ReportSubCategory $reportSubCategory
     */
    public function __construct(ReportCategory $reportCategory, ReportSubCategory $reportSubCategory)
    {
        $this->reportCategory = $reportCategory;
        $this->reportSubCategory = $reportSubCategory;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getUploadDate(): \DateTime
    {
        return $this->uploadDate;
    }

    /**
     * @param \DateTime $uploadDate
     * @return void
     */
    public function setUploadDate(\DateTime $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return void
     */
    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return ReportCategory
     */
    public function getReportCategory(): ReportCategory
    {
        return $this->reportCategory;
    }

    /**
     * @param ReportCategory $reportCategory
     * @return Report
     */
    public function setReportCategory(ReportCategory $reportCategory): self
    {
        $this->reportCategory = $reportCategory;

        return $this;
    }

    /**
     * @return ReportSubCategory
     */
    public function getReportSubCategory(): ReportSubCategory
    {
        return $this->reportSubCategory;
    }

    /**
     * @param ReportSubCategory $reportSubCategory
     * @return Report
     */
    public function setReportSubCategory(ReportSubCategory $reportSubCategory): self
    {
        $this->reportSubCategory = $reportSubCategory;

        return $this;
    }
}
