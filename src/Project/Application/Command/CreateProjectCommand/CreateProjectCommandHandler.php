<?php


namespace Overseer\Project\Application\Command\CreateProjectCommand;


use Overseer\Project\Domain\Command\CreateProjectCommand;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;
use Overseer\Project\Domain\ValueObject\ProjectTitle;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class CreateProjectCommandHandler implements CommandHandler
{
    private CreateProjectCommandValidator $validator;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(CreateProjectCommandValidator $validator, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof CreateProjectCommand;
    }

    public function __invoke(CreateProjectCommand $command): void
    {
        $this->validator->validate($command);

        $projectId = ProjectId::fromString($command->getProjectId());
        $projectTitle = new ProjectTitle($command->getProjectTitle());
        $projectSlug = new Slug($command->getProjectSlug());
        $projectMemberUsername = new ProjectMemberUsername($command->getProjectOwner());

        $project = Project::create(
            $projectId,
            $projectTitle,
            $projectSlug,
            $projectMemberUsername,
            $command->getDescription()
        );

        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}