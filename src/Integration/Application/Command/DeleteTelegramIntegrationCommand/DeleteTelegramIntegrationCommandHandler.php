<?php


namespace Overseer\Integration\Application\Command\DeleteTelegramIntegrationCommand;


use Overseer\Integration\Domain\Command\DeleteTelegramIntegrationCommand;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;

class DeleteTelegramIntegrationCommandHandler implements CommandHandler
{
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;
    private DeleteTelegramIntegrationCommandValidator $validator;

    public function __construct(TelegramIntegrationReadModel $telegramIntegrationReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel, DeleteTelegramIntegrationCommandValidator $validator)
    {
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof DeleteTelegramIntegrationCommand;
    }

    public function __invoke(DeleteTelegramIntegrationCommand $command)
    {
        $this->validator->validate($command);

        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($command->getId()));
        $this->telegramIntegrationWriteModel->delete($integration);
    }
}