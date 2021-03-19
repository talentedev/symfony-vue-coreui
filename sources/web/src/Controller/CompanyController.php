<?php

namespace App\Controller;

use App\Entity\Company;
use App\Service\CompanyService;
use App\Service\CountryService;
use App\Service\GroupService;
use App\Service\ProductService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CompanyController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class CompanyController extends AppController
{
    /** @var CompanyService $companyService */
    private $companyService;

    /** @var CountryService $countryService */
    private $countryService;

    /** @var GroupService $groupService */
    private $groupService;

    /** @var ProductService $productService */
    private $productService;

    public function __construct(CompanyService $companyService, CountryService $countryService, GroupService $groupService, ProductService $productService)
    {
        $this->companyService = $companyService;
        $this->countryService = $countryService;
        $this->groupService = $groupService;
        $this->productService = $productService;
    }

    /**
     * @Rest\Get("/api/companies", name="getCompanies")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanies(Request $request): JsonResponse
    {
        $groupId = $request->get('groupId');
        $countries = $this->companyService->getCompaniesByGroup($groupId);
        return $this->respond($countries);
    }

    /**
     * @Rest\Post("/api/save-company", name="createCompany")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function createCompany(Request $request): JsonResponse
    {
        $groupId = $request->get('groupId');
        $countryId = $request->get('countryId');
        $companyName = $request->get('name');

        $entityManager = $this->getDoctrine()->getManager();

        $group = $this->groupService->getGroupById($groupId);
        $country = $this->countryService->getCountryById($countryId);

        $company = new Company();
        $company->setName($companyName);
        $company->setGroup($group);
        $company->setCountry($country);
        $entityManager->persist($company);
        $entityManager->flush();

        return $this->respond($company);
    }

    /**
     * @Rest\Get("/api/close-company", name="closeCompany")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function closeCompany(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');

        $entityManager = $this->getDoctrine()->getManager();

        $company = $this->companyService->getCompanyById($companyId);
        $company->setStatus('closed');
        $entityManager->persist($company);
        $entityManager->flush();

        // Set all product's status to stopped.
        $products = $this->productService->getAllByCompany($companyId);
        foreach ($products as $product) {
            $product->setStatus('stopped');
            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->respond($company);
    }

    /**
     * @Rest\Get("/api/relaunch-company", name="relaunchCompany")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function relaunchCompany(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');

        $entityManager = $this->getDoctrine()->getManager();

        $company = $this->companyService->getCompanyById($companyId);
        $company->setStatus('active');
        $entityManager->persist($company);
        $entityManager->flush();

        return $this->respond($company);
    }
}
