<?php

namespace App\Service;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * GroupService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Group[]
     */
    public function getAll(): array
    {
        /** @var Group[] $groups */
        $groups = $this->em->getRepository(Group::class)->findAll();
        return $groups;
    }

    /**
     * @param int $groupId
     * @return Group
     */
    public function getGroupById(int $groupId): Group
    {
        /** @var Group $group */
        $group = $this->em->getRepository(Group::class)->find($groupId);
        return $group;
    }
}
