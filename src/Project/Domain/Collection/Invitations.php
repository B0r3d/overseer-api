<?php


namespace Overseer\Project\Domain\Collection;


use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;

class Invitations extends \ArrayObject
{
    public function findInvitationWithId(ProjectMemberInvitationId $invitationId): ?ProjectMemberInvitation
    {
        /** @var ProjectMemberInvitation $invitation */
        foreach ($this as $invitation) {
            if ($invitation->getId()->equals($invitationId)) {
                return $invitation;
            }
        }

        return null;
    }

    public function findInvitationWithUsername(Username $username): ?ProjectMemberInvitation
    {
        /** @var ProjectMemberInvitation $invitation */
        foreach ($this as $invitation) {
            if ($invitation->getUsername()->equals($username)) {
                return $invitation;
            }
        }

        return null;
    }

    public function removeInvitation(ProjectMemberInvitation $invitation): void
    {
        /** @var ProjectMemberInvitation $inv */
        foreach($this as $index => $inv) {
            if ($inv === $invitation) {
                unset($this->invitations[$index]);
                break;
            }
        }
    }
}