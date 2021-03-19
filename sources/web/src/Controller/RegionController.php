<?php

namespace App\Controller;

use App\Service\RegionService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class RegionController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class RegionController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var RegionService $regionService */
    private $regionService;

    public function __construct(SerializerInterface $serializer, RegionService $regionService)
    {
        $this->serializer = $serializer;
        $this->regionService = $regionService;
    }

    /**
     * @Rest\Get("/api/regions", name="getRegions")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getRegions(Request $request): JsonResponse
    {
        $regions = $this->regionService->getAll();
        $data = $this->serializer->serialize($regions, 'json');
        return new JsonResponse($data, 200, [], true);
    }
}
