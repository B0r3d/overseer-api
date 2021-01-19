<?php


namespace Overseer\Integration\Application\Command\UpdateWebhookIntegrationCommand;


use Overseer\Integration\Domain\Command\UpdateWebhookCommand;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\ValueObject\Url;

class UpdateWebhookIntegrationCommandHandler implements CommandHandler
{
    private WebhookIntegrationReadModel $integrationReadModel;
    private WebhookIntegrationWriteModel $integrationWriteModel;
    private UpdateWebhookIntegrationCommandValidator $validator;

    public function __construct(WebhookIntegrationReadModel $integrationReadModel, WebhookIntegrationWriteModel $integrationWriteModel, UpdateWebhookIntegrationCommandValidator $validator)
    {
        $this->integrationReadModel = $integrationReadModel;
        $this->integrationWriteModel = $integrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof UpdateWebhookCommand;
    }

    public function __invoke(UpdateWebhookCommand $command)
    {
        $this->validator->validate($command);

        $integration = $this->integrationReadModel->findById(IntegrationId::fromString($command->getId()));

        if ($command->getUrl()) {
            $integration->setUrl(new Url($command->getUrl()));
        }

        $integration->setFilters(new Filters($command->getFilters()));

        $this->integrationWriteModel->save($integration);
    }
}