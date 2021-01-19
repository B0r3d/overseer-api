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
            case ProjectMemberPermission::INVITE_NEW_MEMBERS():
            case ProjectMemberPermission::REMOVE_PROJECT_MEMBERS():
            case ProjectMemberPermission::CANCEL_INVITATION():
            case ProjectMemberPermission::MANAGE_WEBHOOK_INTEGRATION():
            case ProjectMemberPermission::MANAGE_TELEGRAM_INTEGRATION():
                return $value === $this->project->getProjectOwner()->getUsername()->getValue();
        }
    }
}