<?php


namespace Overseer\Integration\Application\Command\UpdateTelegramIntegrationCommand;


use Overseer\Integration\Domain\Command\UpdateTelegramIntegrationCommand;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;

class UpdateTelegramIntegrationCommandHandler implements CommandHandler
{
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;
    private UpdateTelegramIntegrationCommandValidator $validator;

    public function __construct(TelegramIntegrationReadModel $telegramIntegrationReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel, UpdateTelegramIntegrationCommandValidator $validator)
    {
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof UpdateTelegramIntegrationCommand;
    }

    public function __invoke(UpdateTelegramIntegrationCommand $command)
    {
        $this->validator->validate($command);

        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($command->getId()));

        if ($command->getChatId()) {
            $integration->setChatId($command->getChatId());
        }

        if ($command->getBotId()) {
            $integration->setBotId($command->getBotId());
        }

        $integration->setFilters(new Filters($command->getFilters()));

        $this->telegramIntegrationWriteModel->save($integration);
    }
}