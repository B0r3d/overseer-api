<?php


namespace Overseer\Project\Application\Command\RemoveApiKeyCommand;


use Overseer\Project\Domain\Command\RemoveApiKeyCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ApiKeyId;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class RemoveApiKeyCommandHandler implements CommandHandler
{
    private RemoveApiKeyCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(RemoveApiKeyCommandValidator $validator, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof RemoveApiKeyCommand;
    }

    public function __invoke(RemoveApiKeyCommand $command)
    {
        $this->validator->validate($command);
        $project = $this->projectReadModel->findByUuid(ProjectId::fromString($command->getProjectId()));

        $apiKey = $project->getApiKey(ApiKeyId::fromString($command->getApiKeyId()));
        $project->removeApiKey($apiKey);
        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}