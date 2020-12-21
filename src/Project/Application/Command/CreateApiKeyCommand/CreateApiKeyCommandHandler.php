<?php


namespace Overseer\Project\Application\Command\CreateApiKeyCommand;


use Overseer\Project\Domain\Command\CreateApiKeyCommand;
use Overseer\Project\Domain\Service\ApiKeyFactory;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;

class CreateApiKeyCommandHandler implements CommandHandler
{
    private CreateApiKeyCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private ApiKeyFactory $apiKeyFactory;
    private EventBus $eventBus;

    public function __construct(CreateApiKeyCommandValidator $validator, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, ApiKeyFactory $apiKeyFactory, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->apiKeyFactory = $apiKeyFactory;
        $this->eventBus = $eventBus;
    }


    public function handles(Command $command): bool
    {
        return $command instanceof CreateApiKeyCommand;
    }

    public function __invoke(CreateApiKeyCommand $command)
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));
        if ($command->getExpiryDate()) {
            $datetime = new \DateTime();
            $datetime->setTimestamp($command->getExpiryDate());
            $apiKey = $this->apiKeyFactory->createApiKey($project, new ExpiryDate($datetime));
        } else {
            $apiKey = $this->apiKeyFactory->createApiKey($project);
        }

        $project->addApiKey($apiKey);
        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}