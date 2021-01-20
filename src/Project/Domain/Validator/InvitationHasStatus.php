<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Shared\Domain\Validator\Specification;

class InvitationHasStatus implements Specification
{
    private InvitationStatus $status;

    public function __construct(InvitationStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @var ProjectMemberInvitation $value
     * @return bool
     */
    public function isSatisfiedBy($value): bool
    {
        return $value->getStatus()->equals($this->status);
    }
}