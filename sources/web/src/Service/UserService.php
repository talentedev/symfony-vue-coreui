<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Group;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use function Safe\substr;

class UserService
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $login
     * @param string $password
     * @param string $role
     * @param string $email
     * @param Group $group
     * @param string $token
     * @return User
     */
    public function createUser(string $login, string $password, string $role, string $email, Group $group, string $token): User
    {
        $userEntity = new User();

        $userEntity->setLogin($login);
        $userEntity->setPlainPassword($password);
        $userEntity->setRoles([$role]);
        $userEntity->setEmail($email);
        $userEntity->setGroup($group);
        $userEntity->setToken($token);

        $this->em->persist($userEntity);
        $this->em->flush();

        return $userEntity;
    }

    /**
     * @param User $userEntity
     * @param mixed[] $properties
     * @return User
     */
    public function saveUser(User $userEntity, array $properties): User
    {
        $login = "";
        $password = "";
        $role = "";
        $email = "";
        $token = "";
        if (isset($properties['login'])) {
            $login = $properties['login'];
        }
        if (isset($properties['password'])) {
            $password = $properties['password'];
            $userEntity->setPlainPassword($password);
        }
        if (isset($properties['role'])) {
            $role = $properties['role'];
        }
        if (isset($properties['email'])) {
            $email = $properties['email'];
        }
        if (isset($properties['group'])) {
            $group = $properties['group'];
        }
        if (isset($properties['token'])) {
            $token = $properties['token'];
        }

        $userEntity->setLogin($login?$login:$userEntity->getLogin());
        if (!isset($password)) {
            $userEntity->setPlainPassword($password);
        }
        $userEntity->setRoles($role ? [$role] : $userEntity->getRoles());
        $userEntity->setEmail($email ? $email : $userEntity->getEmail());
        $userEntity->setToken($token ? $token : $userEntity->getToken());
        $this->em->persist($userEntity);
        $this->em->flush();
        return $userEntity;
    }

    /**
     * @param string $rememberMeToken
     * @return User|null
     */
    public function getUserByRememberMeToken(string $rememberMeToken): ?User
    {
        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
           'rememberMeToken' => $rememberMeToken,
        ]);
        if ($user === null) {
            return $user;
        }
        // verify if token is still valid.
        $createdAt = $user->getRememberMeTokenCreated();
        if ($createdAt === null) {
            // should not happen.
            throw new \RuntimeException('remember me token creation date is null');
        }
        $createdAt = Carbon::createFromTimestamp($createdAt->getTimestamp());
        $currentDate = Carbon::now();
        if ($currentDate->diffInDays($createdAt) > 7) {
            $user->setRememberMeToken(null);
            $user->setRememberMeTokenCreated(null);
            $this->em->persist($user);
            $this->em->flush();
            return null;
        }
        return $user;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function setRememberMeToken(User $user): void
    {
        $user->setRememberMeToken(Uuid::uuid4()->toString());
        $user->setRememberMeTokenCreated(Carbon::now());
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param mixed[] $groupIds
     * @return User[]
     */
    public function getUsers(array $groupIds): array
    {
        $criteria = array();

        if ($groupIds) {
            $criteria['group'] = $groupIds;
        }
        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findBy($criteria);
        return $users;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        // FIXME: use constants.
        return [
            'ADMIN',
            'SUPER-USER',
            'USER',
        ];
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByOneEmail(string $email) : ?User
    {
        /** @var User|null  $user */
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => $email));
        return $user;
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function getUserByOneToken(string $token) : ?User
    {
        /** @var User|null  $user */
        $user = $this->em->getRepository(User::class)->findOneBy(array('token' => $token));
        return $user;
    }

    /**
     * @param int $length
     * @param string $chars
     * @return string
     */
    public function randPassword(int $length = 8, string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        return substr(str_shuffle($chars), 0, $length);
    }
}
