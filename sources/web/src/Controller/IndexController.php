<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Safe\json_encode;

final class IndexController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="index")
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Group $group */
        $group = !empty($user) ? $user->getGroup() : [];
        return $this->render('base.html.twig', [
            'isAuthenticated' => json_encode(!empty($user)),
            'roles' => json_encode(!empty($user) ? $user->getRoles() : []),
            'group' => json_encode(!empty($group) ? array('id' => $group->getId(), 'name' => $group->getName()) : []),
            'rememberMeToken' => json_encode(!empty($user) ? $user->getRememberMeToken() : null)
        ]);
    }
}
