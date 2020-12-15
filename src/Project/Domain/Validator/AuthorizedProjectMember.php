<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Shared\Domain\Validator\Specification;

class AuthorizedProjectMember implements Specification
{
    private Project $project;
    private ProjectMemberPermission $projectMemberPermission;

    public function __construct(Project $project, ProjectMemberPermission $projectMemberPermission)
    {
        $this->project = $project;
        $this->projectMemberPermission = $projectMemberPermission;
    }

    public function isSatisfiedBy($value): bool
    {
        switch($this->projectMemberPermission) {
            case ProjectMemberPermission::REMOVE_API_KEY():
            case ProjectMemberPermission::CREATE_API_KEY():
                return $value === $this->project->getProjectOwner()->getValue();
        }
    }
}