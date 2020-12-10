<?php


namespace Overseer\Project\Application\Command\CreateProject;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Exception\ProjectExistsException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Project\Domain\ValueObject\ProjectTitle;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

final class CreateProjectHandler implements CommandHandler
{
    private EventBus $eventBus;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;

    public function __construct(EventBus $eventBus, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel)
    {
        $this->eventBus = $eventBus;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
    }

    public function __invoke(CreateProject $command): void
    {
        $projectTitle = new ProjectTitle($command->projectTitle());
        $projectId = ProjectId::fromString($command->projectId());
        $projectSlug = new Slug($command->projectSlug());
        $projectOwner = new ProjectOwner($command->projectOwner());

        $dbProject = $this->projectReadModel->findBySlug($projectSlug);

        if ($dbProject) {
            throw ProjectExistsException::withSlug($projectSlug);
        }

        $project = Project::create(
            $projectId,
            $projectTitle,
            $projectSlug,
            $projectOwner,
            $command->description()
        );

        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}