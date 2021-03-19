<?php

namespace App\Service;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;

class CountryService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * CountryService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed[] $regionIds
     * @return Country[]
     */
    public function getAll(array $regionIds): array
    {
        $criteria = array();

        if ($regionIds) {
            $criteria['region'] = $regionIds;
        }
        /** @var Country[] $countries */
        $countries = $this->em->getRepository(Country::class)->findBy($criteria, array('name' => 'ASC'));
        return $countries;
    }

    /**
     * @param int $countryId
     * @return Country
     */
    public function getCountryById(int $countryId): Country
    {
        /** @var Country $country */
        $country = $this->em->getRepository(Country::class)->find($countryId);
        return $country;
    }
}
