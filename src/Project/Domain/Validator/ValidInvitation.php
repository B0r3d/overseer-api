<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Shared\Domain\Validator\Specification;

class ValidInvitation implements Specification
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function isSatisfiedBy($value): bool
    {
        $invitation = $this->project->getInvitations()->findInvitationWithId(ProjectMemberInvitationId::fromString($value));
        return $invitation !== null;
    }
}