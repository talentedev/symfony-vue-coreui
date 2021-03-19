<?php

namespace App\Controller;

use App\Entity\Group;
use App\Service\GroupService;
use Safe\Exceptions\JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class GroupController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class GroupController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var GroupService $groupService */
    private $groupService;

    public function __construct(SerializerInterface $serializer, GroupService $groupService)
    {
        $this->serializer = $serializer;
        $this->groupService = $groupService;
    }

    /**
     * @Rest\Get("/api/groups", name="getAll")
     * @Security("has_role('ADMIN') or has_role('SUPER-USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        $groups = $this->groupService->getAll();
        $data = $this->serializer->serialize($groups, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Post("/api/create-group", name="createGroup")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function createGroup(Request $request): JsonResponse
    {
        $name = $request->get('name');

        $entityManager = $this->getDoctrine()->getManager();

        $group = new Group();
        $group->setName($name);
        $entityManager->persist($group);
        $entityManager->flush();

        $data = $this->serializer->serialize($group, 'json');
        return new JsonResponse($data, 200, [], true);
    }
}
