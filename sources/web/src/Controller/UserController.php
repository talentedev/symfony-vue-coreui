<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Group;
use App\Service\MailService;
use App\Service\UserService;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var UserService $userService */
    private $userService;

    /** @var MailService $mailService */
    private $mailService;

    public function __construct(SerializerInterface $serializer, UserService $userService, MailService $mailService)
    {
        $this->serializer = $serializer;
        $this->userService = $userService;
        $this->mailService = $mailService;
    }

    /**
     * @Rest\Post("/api/user/create", name="createUser")
     * @Rest\Put("/api/user/update", name="updateUser")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function createUserAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $userEntity = null;
        $userId = $request->request->get('userId');
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        if (!isset($password)) {
            $password = $this->userService->randPassword();
        }
        $role = $request->request->get('role');
        $email = $request->request->get('email');
        $groupId = $request->request->get('group');
        /** @var Group  $group */
        $group = $em->getRepository(Group::class)->find($groupId);

        /** @var User|null  $user */
        $user = $userId && $em->getRepository(User::class)->find($userId);
        if (!$user) {
            $userEntity = $this->userService->createUser($login, $password, $role, $email, $group, '');

            // $params = [
            //     'login' => $userEntity->getLogin(),
            //     'password' => $password,
            //     'token' => 'test-token'
            // ];

            // $this->mailService->send(
            //     'Création de compte Web',
            //     'contact@Web.fr',
            //     $userEntity->getEmail(),
            //     'mail/registration.html.twig',
            //     $params
            // );
        } else {
            $params = [
                'login' => $login,
                'password' => $password,
                'role' => $role,
                'email' => $email,
                'group' => $group
            ];
            $userEntity = $this->userService->saveUser($user, $params);
        }
        $data = $this->serializer->serialize($userEntity, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Post("/api/user/register", name="registerUser")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function registerUserAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $login = $request->request->get('login');
        $email = $request->request->get('login');
        $role = $request->request->get('role');
        $groupId = $request->request->get('group');
        $password = '';
        $token = $this->userService->randPassword(10);
        /** @var Group  $group */
        $group = $em->getRepository(Group::class)->find($groupId);

        $userEntity = $this->userService->getUserByOneEmail($email);
        if ($userEntity  === null) {
            $userEntity = $this->userService->createUser($login, $password, $role, $email, $group, $token);

            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $params = [
                'activationUrl' => $baseUrl . '/password-registration?token=' . $token,
                'login' => $login
            ];

            $mailState = $this->mailService->send(
                'Set your password for Web Extranet',
                'Web@web.org',
                $email,
                'mail/activation.html.twig',
                $params
            );

            if (!$mailState) {
                $data = $this->serializer->serialize([
                    'message' => 'Impossible to send mail. Please try again. Contact the administrator if the problem persist.'
                ], 'json');
                return new JsonResponse($data, 500, [], true);
            }

            $data = $this->serializer->serialize($userEntity, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            $data = $this->serializer->serialize([
                'message' => $userEntity->getLogin() .' already exists.'
            ], 'json');
            return new JsonResponse($data, 409, [], true);
        }
    }

    /**
     * @Rest\Post("/api/user/activate", name="activateUser")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function activateUserAction(Request $request): JsonResponse
    {
        $token = $request->request->get('token');
        $password = $request->request->get('password');

        /** @var User|null  $user */
        $user = $this->userService->getUserByOneToken($token);
        if ($user) {
            $this->userService->saveUser($user, ['password' => $password]);
        } else {
            $response = array('status' => 'error');
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 500, [], true);
        }

        $data = $this->serializer->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @route("/api/users", name="users")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAction(Request $request) : JsonResponse
    {
        $groupIds = [];
        if ($request->get('groupIds')) {
            $groupIds = explode(',', $request->get('groupIds'));
        }
        $users = $this->userService->getUsers($groupIds);
        $data = $this->serializer->serialize($users, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @route("/api/roles", name="getRoles")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Security("has_role('ADMIN')")
     * @return JsonResponse
     */
    public function getRoles() : JsonResponse
    {
        $roles = $this->userService->getRoles();
        $data = $this->serializer->serialize($roles, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Rest\Get("/api/user/delete", name="deleteUser")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Security("has_role('ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteUser(Request $request): JsonResponse
    {
        $user_id = $request->get('user_id');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);

        if ($user != null) {
            $em->remove($user);
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
}
