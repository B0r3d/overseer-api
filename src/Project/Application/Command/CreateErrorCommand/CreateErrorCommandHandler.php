<?php


namespace Overseer\Project\Application\Command\CreateErrorCommand;


use Overseer\Project\Domain\Command\CreateErrorCommand;
use Overseer\Project\Domain\Service\ErrorFactory;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class CreateErrorCommandHandler implements CommandHandler
{
    private CreateErrorCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ErrorFactory $errorFactory;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(CreateErrorCommandValidator $validator, ProjectReadModel $projectReadModel, ErrorFactory $errorFactory, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->errorFactory = $errorFactory;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof CreateErrorCommand;
    }

    public function __invoke(CreateErrorCommand $command)
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        $exception = $this->errorFactory->createException(
            $command->getClass(),
            $command->getErrorCode(),
            $command->getErrorMessage(),
            $command->getLine(),
            $command->getFile()
        );

        $stacktrace = $this->errorFactory->createStacktrace($command->getStacktrace());

        $error = $this->errorFactory->createError(
            $project,
            ErrorId::fromString($command->getErrorId()),
            (new \DateTime())->setTimestamp($command->getOccurredAt()),
            $exception,
            $stacktrace
        );

        $project->addError($error);
        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}