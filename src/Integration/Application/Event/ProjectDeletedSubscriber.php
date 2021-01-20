<?php


namespace Overseer\Integration\Application\Event;


use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Overseer\Project\Domain\Event\ProjectDeleted;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\Shared\Domain\ValueObject\Uuid;

final class ProjectDeletedSubscriber implements EventSubscriber
{
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;
    private WebhookIntegrationWriteModel $webhookIntegrationWriteModel;
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;

    public function __construct(WebhookIntegrationReadModel $webhookIntegrationReadModel, WebhookIntegrationWriteModel $webhookIntegrationWriteModel, TelegramIntegrationReadModel $telegramIntegrationReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel)
    {
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
        $this->webhookIntegrationWriteModel = $webhookIntegrationWriteModel;
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            ProjectDeleted::class,
        ];
    }

    public function __invoke(ProjectDeleted $event)
    {
        $integrations = $this->webhookIntegrationReadModel->findAllByProjectId(Uuid::fromString($event->aggregateId()));

        /** @var WebhookIntegration $integration */
        foreach ($integrations as $integration) {
            $this->webhookIntegrationWriteModel->delete($integration);
        }

        $integrations = $this->telegramIntegrationReadModel->findAllByProjectId(Uuid::fromString($event->aggregateId()));

        /** @var TelegramIntegration $integration */
        foreach ($integrations as $integration) {
            $this->telegramIntegrationWriteModel->delete($integration);
        }
    }
}