<?php

namespace App\Service;

use App\Entity\ImportedData;
use App\Repository\ImportedDataRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportedDataService
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * ImportedDataService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        /** @var ImportedDataRepository $importedDataRepository */
        $importedDataRepository = $this->em->getRepository(ImportedData::class);
        /** @var ImportedData[] $importedData */
        $importedData = $importedDataRepository->getResult($commodites, $types, $countries, $from, $to);
        return $importedData;
    }

    /**
     * @return ImportedData[]
     */
    public function getCommoditiesTypes(): array
    {
        /** @var ImportedDataRepository $importedDataRepository */
        $importedDataRepository = $this->em->getRepository(ImportedData::class);
        /** @var ImportedData[] $importedData */
        $importedData = $importedDataRepository->getCommoditiesTypes();
        return $importedData;
    }

    /**
     * @return ImportedData[]
     */
    public function getCountryRegionPairs(): array
    {
        /** @var ImportedDataRepository $importedDataRepository */
        $importedDataRepository = $this->em->getRepository(ImportedData::class);
        /** @var ImportedData[] $importedData */
        $importedData = $importedDataRepository->getCountryRegionPairs();
        return $importedData;
    }
}
