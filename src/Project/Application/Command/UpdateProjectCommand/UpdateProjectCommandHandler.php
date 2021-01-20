<?php


namespace Overseer\Project\Application\Command\UpdateProjectCommand;


use Overseer\Project\Domain\Command\UpdateProjectCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;

class UpdateProjectCommandHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private UpdateProjectCommandValidator $validator;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, UpdateProjectCommandValidator $validator)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof UpdateProjectCommand;
    }

    public function __invoke(UpdateProjectCommand $command)
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        if ($command->getDescription()) {
            $project->setDescription($command->getDescription());
        }

        $this->projectWriteModel->save($project);
    }
}