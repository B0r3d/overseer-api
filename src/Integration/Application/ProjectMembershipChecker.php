<?php


namespace Overseer\Integration\Application;


use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\ValueObject\Uuid;

class ProjectMembershipChecker
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function isMember(Uuid $projectId, string $username): bool
    {
        $projectId = ProjectId::fromString($projectId->value());
        $project = $this->projectReadModel->findById($projectId);
        $username = new Username($username);

        $member = $project->getMembers()->findMemberWithUsername($username);

        return $member !== null;
    }
}