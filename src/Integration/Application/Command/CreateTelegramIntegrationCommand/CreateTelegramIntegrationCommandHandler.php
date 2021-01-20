<?php


namespace Overseer\Integration\Application\Command\CreateTelegramIntegrationCommand;


use Overseer\Integration\Domain\Command\CreateTelegramIntegrationCommand;
use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\ValueObject\Uuid;

class CreateTelegramIntegrationCommandHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;
    private CreateTelegramIntegrationCommandValidator $validator;

    public function __construct(ProjectReadModel $projectReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel, CreateTelegramIntegrationCommandValidator $validator)
    {
        $this->projectReadModel = $projectReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof CreateTelegramIntegrationCommand;
    }

    public function __invoke(CreateTelegramIntegrationCommand $command)
    {
        $this->validator->validate($command);

        $filters = new Filters($command->getFilters());

        $integration = new TelegramIntegration(
            IntegrationId::fromString($command->getId()),
            Uuid::fromString($command->getProjectId()),
            $command->getBotId(),
            $command->getChatId(),
            $filters
        );

        $this->telegramIntegrationWriteModel->save($integration);
    }
}