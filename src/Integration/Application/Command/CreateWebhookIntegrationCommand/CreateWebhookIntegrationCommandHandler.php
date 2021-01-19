<?php


namespace Overseer\Integration\Application\Command\CreateWebhookIntegrationCommand;


use Overseer\Integration\Domain\Command\CreateWebhookIntegrationCommand;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\ValueObject\Url;
use Overseer\Shared\Domain\ValueObject\Uuid;

class CreateWebhookIntegrationCommandHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private WebhookIntegrationWriteModel $webhookIntegrationWriteModel;
    private CreateWebhookIntegrationCommandValidator $validator;

    public function __construct(ProjectReadModel $projectReadModel, WebhookIntegrationWriteModel $webhookIntegrationWriteModel, CreateWebhookIntegrationCommandValidator $validator)
    {
        $this->projectReadModel = $projectReadModel;
        $this->webhookIntegrationWriteModel = $webhookIntegrationWriteModel;
        $this->validator = $validator;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof CreateWebhookIntegrationCommand;
    }

    public function __invoke(CreateWebhookIntegrationCommand $command)
    {
        $this->validator->validate($command);

        $filters = new Filters($command->getFilters());
        $url = new Url($command->getUrl());

        $integration = new WebhookIntegration(
            IntegrationId::fromString($command->getId()),
            Uuid::fromString($command->getProjectId()),
            $url,
            $filters
        );

        $this->webhookIntegrationWriteModel->save($integration);
    }
}