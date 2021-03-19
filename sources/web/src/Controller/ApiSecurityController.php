<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\WebException;
use App\Service\MailService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiSecurityController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var UserService $userService */
    private $userService;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var MailService $mailService */
    private $mailService;

    /**
     * ApiSecurityController constructor.
     * @param SerializerInterface $serializer
     * @param UserService $userService
     * @param TokenStorageInterface $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param MailService $mailService
     */
    public function __construct(SerializerInterface $serializer, UserService $userService, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher, MailService $mailService)
    {
        $this->serializer = $serializer;
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailService = $mailService;
    }

    /**
     * @Route("/api/security/login", name="login")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function loginAction(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $rememberMe = $request->request->get('rememberMe');
        if ($rememberMe === true) {
            $this->userService->setRememberMeToken($user);
        }
        $group = $user->getGroup();

        if (!empty($group)) {
            $response = array(
                'rememberMeToken' => $user->getRememberMeToken(),
                'roles' => $user->getRoles(),
                'group' => [
                    'id' => $group->getId(),
                    'name' => $group->getName()
                ]
            );
        } else {
            $response = array(
                'rememberMeToken' => $user->getRememberMeToken(),
                'roles' => $user->getRoles(),
                'group' => null
            );
        }

        $response = new JsonResponse($response);
        return $response;
    }


    /**
     * @Route("/api/security/remember-me-login", name="rememberMeLogin")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function rememberMeLoginAction(Request $request): JsonResponse
    {
        $rememberMeToken = $request->request->get('rememberMeToken');
        $user = $this->userService->getUserByRememberMeToken($rememberMeToken);
        if ($user === null) {
            return new JsonResponse(null, 403);
        }
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
        // Fire the login event.
        // Logging the user in above the way we do it doesn't do this automatically.
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
        //return $this->loginAction($request);
        return new JsonResponse();
    }

    /**
     * @Route("/api/security/logout", name="logout")
     * @return void
     * @throws \RuntimeException
     */
    public function logoutAction(): void
    {
        throw new \RuntimeException('This should not be reached!');
    }

    /**
     * @Rest\Post("/api/security/forgetPassword", name="forget-password")
     * @throws WebException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $email = $request->request->get('email');
        $token = $this->userService->randPassword(10);

        $userEntity = $this->userService->getUserByOneEmail($email);

        if ($userEntity  !== null) {
            $params = [
                'token' => $token
            ];
            $userEntity = $this->userService->saveUser($userEntity, $params);
            $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $params = [
                'activationUrl' => $baseUrl . '/reset-password?token=' . $token,
                'login' => $email
            ];

            $mailState = $this->mailService->send(
                'Reset your password for Web Extranet',
                'Web@manganese.org',
                $email,
                'mail/reset-password.html.twig',
                $params
            );

            if (!$mailState) {
                $data = $this->serializer->serialize([
                    'status' => 'error',
                    'message' => 'Impossible to send mail. Please try again. Contact the administrator if the problem persist.'
                ], 'json');
                return new JsonResponse($data, 500, [], true);
            }

            $data = $this->serializer->serialize($userEntity, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Invalid email. Make sure your email is correct and please try again.');
            $data = $this->serializer->serialize($response, 'json');
            return new JsonResponse($data, 404, [], true);
        }
    }

    /**
     * @Rest\Post("/api/security/reset-password", name="resetPassword")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function resetPasswordAction(Request $request): JsonResponse
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
            return new JsonResponse($data, 404, [], true);
        }

        $data = $this->serializer->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
