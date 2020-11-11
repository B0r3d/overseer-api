<?php


namespace Overseer\Project\Application\Command\RemoveMember;


use Overseer\Project\Domain\Exception\ProjectMemberNotFoundException;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Exception\UnauthorizedException;

final class RemoveMemberHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
    }

    public function __invoke(RemoveMember $command): void
    {
        $projectMemberId = ProjectMemberId::fromString($command->projectMemberId());
        $project = $this->projectReadModel->findByProjectMemberId($projectMemberId);

        if (!$project) {
            throw ProjectNotFoundException::withProjectMemberId($projectMemberId);
        }

        $projectMember = $project->findMemberWithId($projectMemberId);

        if (!$projectMember) {
            throw ProjectMemberNotFoundException::withProjectMemberId($projectMemberId);
        }

        $issuedBy = new ProjectOwner($command->issuedBy());
        if (!$project->projectOwner()->equals($issuedBy)) {
            throw new UnauthorizedException();
        }

        $project->removeMember($projectMember);
        $this->projectWriteModel->save($project);
    }
}