<?php


namespace Overseer\Integration\Application\Command\DeleteWebhookIntegrationCommand;


use Overseer\Integration\Domain\Command\DeleteWebhookIntegrationCommand;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;

class DeleteWebhookIntegrationCommandHandler implements CommandHandler
{
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;
    private WebhookIntegrationWriteModel $webhookIntegrationWriteModel;
    private DeleteWebhookIntegrationCommandValidator $validator;

    public function __construct(WebhookIntegrationReadModel $webhookIntegrationReadModel, WebhookIntegrationWriteModel $webhookIntegrationWriteModel, DeleteWebhookIntegrationCommandValidator $validator)
    {
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
        $this->webhookIntegrationWriteModel = $webhookIntegrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof DeleteWebhookIntegrationCommand;
    }

    public function __invoke(DeleteWebhookIntegrationCommand $command)
    {
        $this->validator->validate($command);

        $integration = $this->webhookIntegrationReadModel->findById(IntegrationId::fromString($command->getId()));
        $this->webhookIntegrationWriteModel->delete($integration);
    }
}