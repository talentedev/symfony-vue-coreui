<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\ReportCategory;
use App\Service\ReportService;
use Safe\Exceptions\JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use \DateTime;

/**
 * Class ReportController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ReportController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var ReportService $reportService */
    private $reportService;

    /** @var Filesystem $fileSystem */
    private $fileSystem;

    public function __construct(SerializerInterface $serializer, ReportService $reportService, Filesystem $fileSystem)
    {
        $this->serializer = $serializer;
        $this->reportService = $reportService;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @Rest\Get("/api/report-categories", name="getReportCategories")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getReportCategories(Request $request): JsonResponse
    {
        $categories = $this->reportService->getReportCategories();
        $data = $this->serializer->serialize($categories, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/report-sub-categories", name="getReportSubCategories")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getReportSubCategories(Request $request): JsonResponse
    {
        $categoryIds = $request->get('report_categories_id');
        $subCategories = $this->reportService->getReportSubCategories(explode(',', $categoryIds));
        $data = $this->serializer->serialize($subCategories, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/reports", name="getReports")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getReports(Request $request): JsonResponse
    {
        $categoryIds = [];
        $subCategoryIds = [];
        if ($request->get('report_categories_id')) {
            $categoryIds = explode(',', $request->get('report_categories_id'));
        }
        if ($request->get('report_sub_categories_id')) {
            $subCategoryIds = explode(',', $request->get('report_sub_categories_id'));
        }
        $offset = $request->get('offset');
        $reports = $this->reportService->getReports($categoryIds, $subCategoryIds, $offset);
        $data = $this->serializer->serialize($reports, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Post("/api/save-report", name="saveReport")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function saveReport(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $date = $request->get('date');
        $categoryId = $request->get('report_categories_id');
        $subCategoryId = $request->get('report_sub_categories_id');
        $file = $request->files->get('file');
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if (!is_null($file)) {
            $filename = uniqid().".".$file->getClientOriginalExtension();
            $path = $this->getParameter('reports_dir');
            $file->move($path, $filename);

            $entityManager = $this->getDoctrine()->getManager();

            $reportCategory = $this->reportService->getReportCategory($categoryId);
            $reportSubCategory = $this->reportService->getReportSubCategory($subCategoryId);

            $report = new Report($reportCategory, $reportSubCategory);
            $report->setName($name);
            $report->setUploadDate(new DateTime($date));
            $report->setReportCategory($reportCategory);
            $report->setReportSubCategory($reportSubCategory);
            $report->setFile($baseurl . '/api/reports/download/' . $filename);

            $entityManager->persist($report);

            $entityManager->flush();

            $data = $this->serializer->serialize($report, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            $response = array(
                'status' => 'error'
            );
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 500, [], true);
        }
    }

    /**
     * @Rest\Get("/api/delete-report", name="deleteReport")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteReport(Request $request): JsonResponse
    {
        $reportId = $request->get('report_id');
        $em = $this->getDoctrine()->getManager();
        $report = $em->getRepository(Report::class)->find($reportId);

        if ($report != null) {
            $this->fileSystem->remove($report->getFile());
            $em->remove($report);
            $em->flush();
            $response = array('status' => 'success');
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            $response = array('status' => 'error');
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 500, [], true);
        }
    }

    /**
     * @Rest\Get("/api/reports/download/{filename}", name="downloadReport")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @param string $filename
     * @return Response
     */
    public function downloadReport(Request $request, string $filename): Response
    {
        if ($filename) {
            $em = $this->getDoctrine()->getManager();
            $reports = $em->getRepository(Report::class)->findBy(['file' => $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/api/reports/download/' . $filename]);
            if (!empty($reports)) {
                /** @var Report $report **/
                foreach ($reports as $report) {
                    $reportExtension = pathinfo($report->getFile(), PATHINFO_EXTENSION);
                    $reportFileName = $reportExtension ? $report->getName() . "." . $reportExtension : $report->getName();
                    $response =  new BinaryFileResponse($this->getParameter('kernel.project_dir') . '/uploads/reports/' . $filename);
                    $response->setContentDisposition(
                        ResponseHeaderBag::DISPOSITION_INLINE,
                        $reportFileName
                    );
                    return $response;
                }
            } else {
                throw $this->createNotFoundException();
            }
        } else {
            throw $this->createNotFoundException();
        }
    }
}
