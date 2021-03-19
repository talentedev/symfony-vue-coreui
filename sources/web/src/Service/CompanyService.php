<?php

namespace App\Service;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

class CompanyService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * CompanyService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Company[]
     */
    public function getAll(): array
    {
        /** @var Company[] $companies */
        $companies = $this->em->getRepository(Company::class)->findAll();
        return $companies;
    }

    /**
     * @param int $companyId
     * @return Company
     */
    public function getCompanyById(int $companyId): Company
    {
        /** @var Company $company */
        $company = $this->em->getRepository(Company::class)->find($companyId);
        return $company;
    }

    /**
     * @param int $groupId
     * @return Company[]
     */
    public function getCompaniesByGroup(int $groupId): array
    {
        /** @var Company[] $companies */
        $companies = $this->em->getRepository(Company::class)
                    ->findBy(array('group' => $groupId), array('name' => 'ASC'));
        return $companies;
    }
}
