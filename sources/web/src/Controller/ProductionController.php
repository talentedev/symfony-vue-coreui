<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Production;
use App\Service\CompanyService;
use App\Service\ProductService;
use App\Service\ProductionService;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as Rest;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Safe\Exceptions\ArrayException;
use Safe\Exceptions\FilesystemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function Safe\ksort;
use function Safe\tempnam;
use \DateTime;
use \DateInterval;

/**
 * Class ProductionController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class ProductionController extends AppController
{
    /** @var CompanyService $companyService */
    private $companyService;

    /** @var ProductService $productService */
    private $productService;

    /** @var ProductionService $productionService */
    private $productionService;

    public function __construct(CompanyService $companyService, ProductService $productService, ProductionService $productionService)
    {
        $this->companyService = $companyService;
        $this->productService = $productService;
        $this->productionService = $productionService;
    }

    /**
     * @Rest\Get("/api/productions", name="getProductionsGroupByRegions")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER')")
     * @param Request $request
     * @return JsonResponse
     * @throws ArrayException
     */
    public function getProductionsGroupByRegions(Request $request): JsonResponse
    {
        $groupId = $request->get('group_id');
        $date = new DateTime($request->get('date'));
        $companies = $this->companyService->getCompaniesByGroup($groupId);

        foreach ($companies as $key => $company) {
            $companyId = $company->getId();
            $products = $this->productService->getAllByCompany($companyId);
            foreach ($products as $index => $product) {
                $productId = $product->getId();
                $productions = $this->productionService->findAllByDate($productId, $date);
                $products[$index]->productions = new ArrayCollection($productions);
            }
            $companies[$key]->products = new ArrayCollection($products);
        }

        $countries = array();
        foreach ($companies as $key => $company) {
            /** @var Country $country */
            $country = $company->getCountry();
            $name = $country->getName();
            $countries[$name][$key] = $company;
        }
        ksort($countries);

        return $this->respond($countries);
    }

    /**
     * @Rest\Post("/api/save-production", name="saveProductions")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function saveProductions(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        $productions = $request->get('productions');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $this->productService->getProductById($productId);
        $productStartDate = $product->getStartDate();

        $changedCapacity = null;

        foreach ($productions as $key => $prod) {
            $startDate = new Datetime($prod['date']);
            $diff = $productStartDate->diff($startDate);

            // Save production only after product start date.
            if (((int)$diff->format('%r%y') * 12 + (int)$diff->format('%r%m')) >= 0) {
                $production = $this->productionService->getProductionByDate($startDate, $productId);

                if ($production) {
                    if ($prod['capacity'] !== null) {
                        if ($prod['capacity'] !== $production->getCapacity() && !$changedCapacity) {
                            $changedCapacity = $prod;
                        }
                        $production->setCapacity($prod['capacity']);
                    }
                    if ($prod['inventory'] !== null) {
                        $production->setInventory($prod['inventory']);
                    }
                    if ($prod['production'] !== null) {
                        $production->setProduction($prod['production']);
                    }

                    $entityManager->persist($production);
                    $entityManager->flush();
                }
            }
        }

        // Modify all capacity from modified month to current one if a capacity is modified.
        if ($changedCapacity) {
            $firstChangedDate = new Datetime($changedCapacity['date']);
            do {
                $production = $this->productionService->getProductionByDate($firstChangedDate, $productId);
                if ($production) {
                    $production->setCapacity($changedCapacity['capacity']);
                    $entityManager->persist($production);
                }
                $firstChangedDate->add(new DateInterval('P1M'));
            } while ($production);

            $entityManager->flush();
        }

        return $this->respond(['status' => 'success']);
    }

    /**
     * @Rest\Get("/api/export-production", name="exportProduction")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return Response
     * @throws ArrayException
     * @throws Exception
     * @throws FilesystemException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportProduction(Request $request): Response
    {
        /** @var String $firstDate */
        $firstDate = $this->productionService->findMinDate()['1'];
        /** @var String $lastDate */
        $lastDate = $this->productionService->findMaxDate()['1'];

        $minDate = new Datetime($firstDate);
        $maxDate = new Datetime($lastDate);

        // Get header titles.
        $header = ['Country', 'Group', 'Company', 'Commodity', 'Type'];

        do {
            $title = $minDate->format('M') . ' ' . $minDate->format('Y');
            array_push($header, $title);
            $diff = $maxDate->diff($minDate);
            $diffInMonth = ((int)$diff->format('%r%y') * 12) + (int)$diff->format('%r%m');
            $minDate->add(new DateInterval('P1M'));
        } while ($diffInMonth < 0);

        $data = array();
        array_push($data, $header);

        // Get cell values.
        $companies = $this->companyService->getAll();

        $countries = array();
        foreach ($companies as $key => $company) {
            /** @var Country $country */
            $country = $company->getCountry();
            $name = $country->getName();
            $countries[$name][$key] = $company;
        }
        ksort($countries);

        foreach ($countries as $country => $companies) {
            foreach ($companies as $company) {
                $products = $company->products;
                foreach ($products as $product) {
                    $row = array(
                        'country' => $country,
                        'group' => $company->getGroup()->getName(),
                        'company' => $company->getName(),
                        'commodity' => $product->getCommodity()->getName(),
                        'type' => 'Production'
                    );
                    for ($i = 5; $i < count($header); $i++) {
                        $row[$header[$i]] = null;
                    }
                    $productions = $product->productions;

                    foreach ($productions as $production) {
                        $date = new Datetime($production->getDate());
                        $row[$date->format('M') . ' ' . $date->format('Y')] = $production->getProduction();
                    }
                    array_push($data, $row);

                    $row['type'] = 'Capacity';
                    foreach ($productions as $production) {
                        $date = new Datetime($production->getDate());
                        $row[$date->format('M') . ' ' . $date->format('Y')] = $production->getCapacity();
                    }
                    array_push($data, $row);

                    $row['type'] = 'Inventory';
                    foreach ($productions as $production) {
                        $date = new Datetime($production->getDate());
                        $row[$date->format('M') . ' ' . $date->format('Y')] = $production->getInventory();
                    }
                    array_push($data, $row);
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Productions");
        $sheet->fromArray($data, null, 'A1');
        
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        
        // Create a Temporary file in the system
        $fileName = 'productions.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
