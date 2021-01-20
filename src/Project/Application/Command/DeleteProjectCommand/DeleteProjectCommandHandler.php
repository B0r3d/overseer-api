<?php


namespace Overseer\Project\Application\Command\DeleteProjectCommand;


use Overseer\Project\Domain\Command\DeleteProjectCommand;
use Overseer\Project\Domain\Event\ProjectDeleted;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class DeleteProjectCommandHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private DeleteProjectCommandValidator $validator;
    private EventBus $eventBus;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, DeleteProjectCommandValidator $validator, EventBus $eventBus)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->validator = $validator;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof DeleteProjectCommand;
    }

    public function __invoke(DeleteProjectCommand $command)
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        $this->projectWriteModel->delete($project);

        $this->eventBus->publish(new ProjectDeleted($project->getId()->value()));
    }
}