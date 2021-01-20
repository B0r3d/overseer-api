<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Shared\Domain\Validator\Specification;

class ValidProjectMember implements Specification
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function isSatisfiedBy($value): bool
    {
        $projectMember = $this->project->getMembers()->findMemberWithId(ProjectMemberId::fromString($value));
        return $projectMember !== null;
    }
}