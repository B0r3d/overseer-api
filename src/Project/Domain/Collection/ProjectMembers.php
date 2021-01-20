<?php


namespace Overseer\Project\Domain\Collection;


use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\Username;

class ProjectMembers extends \ArrayObject
{
    public function findMemberWithUsername(Username $username): ?ProjectMember
    {
        /** @var ProjectMember $member */
        foreach ($this as $member) {
            if ($member->getUsername()->equals($username)) {
                return $member;
            }
        }

        return null;
    }

    public function findMemberWithId(ProjectMemberId $projectMemberId): ?ProjectMember
    {
        /** @var ProjectMember $member */
        foreach($this as $member) {
            if ($member->getId()->equals($projectMemberId)) {
                return $member;
            }
        }

        return null;
    }

    public function removeMember(ProjectMember $projectMember)
    {
        /** @var ProjectMember $member */
        foreach($this as $index => $member) {
            if ($member->getId() === $projectMember->getId()) {
                unset($this[$index]);
                break;
            }
        }
    }
}