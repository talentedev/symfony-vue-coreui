<?php

namespace App\Controller;

use App\Entity\ImportedData;
use App\Service\ImportedDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \DateTime;
use \DateInterval;
use function Safe\tempnam;

/**
 * Class OnlineDatabaseController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class OnlineDatabaseController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var ImportedDataService $importedDataService */
    private $importedDataService;

    public function __construct(SerializerInterface $serializer, ImportedDataService $importedDataService)
    {
        $this->serializer = $serializer;
        $this->importedDataService = $importedDataService;
    }

    /**
     * @return void
     */
    private function deleteOnlineDatabase(): void
    {
        // Delete all data from table.
        $entityManager = $this->getDoctrine()->getManager();
        $cmd = $entityManager->getClassMetadata('App\Entity\ImportedData');
        $connection = $entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @Rest\Post("/api/save-import-online-database", name="saveImportOnlineDatabase")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function saveImportOnlineDatabase(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!is_null($file)) {
            // Delete old data from database
            $this->deleteOnlineDatabase();

            $entityManager = $this->getDoctrine()->getManager();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $worksheetNames = $reader->listWorksheetNames($file);
            foreach ($worksheetNames as $worksheetName) {
                $reader->setLoadSheetsOnly($worksheetName);
                $objPHPExcel = $reader->load($file);
                $objWorksheet = $objPHPExcel->getActiveSheet();
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray = $headingsArray[1];
                $r = -1;
                $namedDataArray = array();
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {
                            $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                        }
                    }
                }

                // Write data to table.
                if (count($namedDataArray) > 0) {
                    $batchSize = 20;
                    foreach ($namedDataArray as $index => $record) {
                        foreach ($record as $key => $value) {
                            if ($key === 'Commodity' || $key === 'Type' || $key === 'Country' || $key === 'Region') {
                                continue;
                            } elseif ($key) {
                                $data = new ImportedData();
                                $data->setCommodity($record['Commodity']);
                                $data->setType($record['Type']);
                                $data->setCountry($record['Country']);
                                $data->setRegion($record['Region']);
                                $date = new DateTime((string)$key);
                                $date->modify('first day of this month');
                                $date->modify('noon');
                                $date->add(new DateInterval('P14D'));
                                $data->setDate($date);

                                if ($value) {
                                    $data->setValue($value);
                                }
                                $entityManager->persist($data);
                            }
                        }
                        if (($index % $batchSize) === 0) {
                            $entityManager->flush();
                            $entityManager->clear();
                        }
                    }
                    $entityManager->flush();
                    $entityManager->clear();
                }
            }

            $response = array(
                'status' => 'success'
            );
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            $response = array(
                'status' => 'No provided data'
            );
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 500, [], true);
        }
    }

    /**
     * @Rest\Get("/api/get-online-database-result", name="getOnlineDatabaseResult")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getOnlineDatabaseResult(Request $request): JsonResponse
    {
        $commodites = [];
        $types = [];
        $countries = [];
        if ($request->get('commodity')) {
            $commodites = explode(',', $request->get('commodity'));
        }
        if ($request->get('type')) {
            $types = explode(',', $request->get('type'));
        }
        if ($request->get('country')) {
            $countries = explode(',', $request->get('country'));
        }
        $from = new DateTime($request->get('from'));
        $from->sub(new DateInterval('P1M'));
        $to = new DateTime($request->get('to'));

        $result = $this->importedDataService->getResult($commodites, $types, $countries, $from, $to);

        $data = $this->serializer->serialize($result, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/get-imported-commodities-types", name="getImportedCommoditiesTypes")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getImportedCommoditiesTypes(Request $request): JsonResponse
    {
        $result = $this->importedDataService->getCommoditiesTypes();
        $data = $this->serializer->serialize($result, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/get-imported-regions-countries", name="getCountryRegionPairs")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCountryRegionPairs(Request $request): JsonResponse
    {
        $result = $this->importedDataService->getCountryRegionPairs();
        $data = $this->serializer->serialize($result, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/get-online-database-file", name="getOnlineDatabaseFile")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return Response
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Safe\Exceptions\FilesystemException
     */
    public function getOnlineDatabaseFile(Request $request): Response
    {
        $commodites = [];
        $types = [];
        $countries = [];
        if ($request->get('commodity')) {
            $commodites = explode(',', $request->get('commodity'));
        }
        if ($request->get('type')) {
            $types = explode(',', $request->get('type'));
        }
        if ($request->get('country')) {
            $countries = explode(',', $request->get('country'));
        }
        $from = new DateTime($request->get('from'));
        $from->modify('first day of this month');
        $to = new DateTime($request->get('to'));
        $to->modify('last day of this month');
        $fileType = $request->get('file_type');

        $result = $this->importedDataService->getResult($commodites, $types, $countries, $from, $to);

        // Get header titles.
        $header = ['Commodity', 'Type', 'Country'];

        $diff = $to->diff($from);
        $diffInMonth = ((int)$diff->format('%r%y') * 12) + (int)$diff->format('%r%m');

        for ($i=1; $i <= abs($diffInMonth) + 1; $i++) {
            $title = $from->format('M') . ' ' . $from->format('Y');
            array_push($header, $title);
            $from->add(new DateInterval('P1M'));
        }

        $data = array();
        array_push($data, $header);

        foreach ($result as $pair) {
            $row = array(
                'commodity' => $pair['commodity'],
                'type' => $pair['type'],
                'country' => $pair['country'],
            );
            $values = explode(';', $pair['data']);
            foreach ($values as $index => $value) {
                $val = explode('=', $value);
                if (count($val) > 0) {
                    $row[$header[$index + 3]] = end($val);
                } else {
                    $row[$header[$index + 3]] = '';
                }
            }
            array_push($data, $row);
        }

        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Online Database");
        $sheet->fromArray($data, null, 'A1');

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $fileName = 'online-database.xlsx';
        if ($fileType === 'csv') {
            $writer = new Csv($spreadsheet);
            $fileName = 'online-database.csv';
        }

        // Create a Temporary file in the system
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        
        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);
        
        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
