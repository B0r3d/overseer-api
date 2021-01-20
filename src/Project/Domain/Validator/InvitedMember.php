<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Validator\Specification;

class InvitedMember implements Specification
{
    private ProjectMemberInvitation $invitation;

    public function __construct(ProjectMemberInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function isSatisfiedBy($value): bool
    {
        $username = new Username($value);
        return $this->invitation->getUsername()->equals($username);
    }
}