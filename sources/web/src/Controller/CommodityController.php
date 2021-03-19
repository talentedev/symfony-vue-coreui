<?php

namespace App\Controller;

use App\Service\CommodityService;
use App\Service\ProductService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CommodityController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class CommodityController extends AppController
{
    /** @var CommodityService $commodityService */
    private $commodityService;

    /** @var ProductService $productService */
    private $productService;

    /**
     * CommodityController constructor.
     *
     * @param CommodityService $commodityService
     * @param ProductService $productService
     */
    public function __construct(CommodityService $commodityService, ProductService $productService)
    {
        $this->commodityService = $commodityService;
        $this->productService = $productService;
    }

    /**
     * @Rest\Get("/api/commodities", name="getCommodities")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCommodities(Request $request): JsonResponse
    {
        $commodities = $this->commodityService->getAll();
        return $this->respond($commodities);
    }

    /**
     * @Rest\Get("/api/stop-commodity", name="stopCommodity")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function stopCommodity(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $this->productService->getProductById($productId);
        $product->setStatus('stopped');
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->respond($product);
    }

    /**
     * @Rest\Get("/api/reopen-commodity", name="reopenCommodity")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function reopenCommodity(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $this->productService->getProductById($productId);
        $product->setStatus('active');
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->respond($product);
    }
}
