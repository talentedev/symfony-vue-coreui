<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Production;
use App\Service\CommodityService;
use App\Service\CompanyService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use \DateTime;
use \DateInterval;

/**
 * Class ProductController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class ProductController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var CommodityService $commodityService */
    private $commodityService;

    /** @var CompanyService $companyService */
    private $companyService;

    public function __construct(CommodityService $commodityService, CompanyService $companyService)
    {
        $encoders = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $this->serializer = new Serializer([$normalizer], [$encoders]);
        $this->commodityService = $commodityService;
        $this->companyService = $companyService;
    }

    /**
     * @Rest\Post("/api/create-product", name="createProduct")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function createProduct(Request $request): JsonResponse
    {
        $commodityId = $request->get('commodityId');
        $companyId = $request->get('companyId');
        $startDate = $request->get('startDate');
        $capacity = $request->get('capacity');

        $entityManager = $this->getDoctrine()->getManager();

        $commodity = $this->commodityService->getCommodityById($commodityId);
        $company = $this->companyService->getCompanyById($companyId);
        $productStartDate = new DateTime($startDate);
        $productStartDate->modify('first day of this month');
        $productStartDate->modify('noon');
        $productStartDate->add(new DateInterval('P14D'));

        $product = new Product();
        $product->setCompany($company);
        $product->setCommodity($commodity);
        $product->setStartDate($productStartDate);
        $entityManager->persist($product);
        $entityManager->flush();

        // Create productions related with the product.
        $now = new DateTime();
        $now->modify('first day of this month');
        $now->modify('noon');
        $now->add(new DateInterval('P14D'));

        $interval = new DateInterval('P1M');  // 1 month
        $diff = $now->diff($productStartDate);
        $monthDiff = (int)$diff->format('%y') * 12 + (int)$diff->format('%m');
        for ($i=0; $i <= $monthDiff; $i++) {
            $production = new Production();
            $production->setDate($productStartDate);
            $production->setProduct($product);
            $production->setCapacity((int)$capacity);
            $entityManager->persist($production);
            $entityManager->flush();

            $productStartDate->add($interval);
        }
        
        $data = $this->serializer->serialize($product, 'json');
        return new JsonResponse($data, 200, [], true);
    }
}
