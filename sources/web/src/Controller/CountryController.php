<?php

namespace App\Controller;

use App\Service\CountryService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CountryController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class CountryController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var CountryService $countryService */
    private $countryService;

    public function __construct(SerializerInterface $serializer, CountryService $countryService)
    {
        $this->serializer = $serializer;
        $this->countryService = $countryService;
    }

    /**
     * @Rest\Get("/api/countries", name="getCountries")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER') or has_role('USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCountries(Request $request): JsonResponse
    {
        $regionIds = [];
        if ($request->get('region_ids')) {
            $regionIds = explode(',', $request->get('region_ids'));
        }
        $countries = $this->countryService->getAll($regionIds);
        $data = $this->serializer->serialize($countries, 'json');
        return new JsonResponse($data, 200, [], true);
    }
}
