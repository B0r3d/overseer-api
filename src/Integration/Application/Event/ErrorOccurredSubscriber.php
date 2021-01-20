<?php


namespace Overseer\Integration\Application\Event;


use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Service\IntegrationMessageSender;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Overseer\Project\Domain\Event\ErrorOccurred;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\Shared\Domain\ValueObject\Uuid;

final class ErrorOccurredSubscriber implements EventSubscriber
{
    private ProjectReadModel $projectReadModel;
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;
    private WebhookIntegrationWriteModel $webhookIntegrationWriteModel;
    private IntegrationMessageSender $webhookMessageSender;
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;
    private IntegrationMessageSender $telegramMessageSender;

    public function __construct(ProjectReadModel $projectReadModel, WebhookIntegrationReadModel $webhookIntegrationReadModel, WebhookIntegrationWriteModel $webhookIntegrationWriteModel, IntegrationMessageSender $webhookMessageSender, TelegramIntegrationReadModel $telegramIntegrationReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel, IntegrationMessageSender $telegramMessageSender)
    {
        $this->projectReadModel = $projectReadModel;
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
        $this->webhookIntegrationWriteModel = $webhookIntegrationWriteModel;
        $this->webhookMessageSender = $webhookMessageSender;
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
        $this->telegramMessageSender = $telegramMessageSender;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            ErrorOccurred::class
        ];
    }

    public function __invoke(ErrorOccurred $event)
    {
        $project = $this->projectReadModel->findById(ProjectId::fromString($event->getProjectId()));
        $error = $project->getErrors()->findById(ErrorId::fromString($event->getErrorId()));

        $webhookIntegrations = $this->webhookIntegrationReadModel->findAllByProjectId(Uuid::fromString($event->getProjectId()));

        /** @var WebhookIntegration $webhookIntegration */
        foreach($webhookIntegrations as $webhookIntegration) {
            if ($webhookIntegration->getFilters()->isFiltered($error->getException()->getClass())) {
                continue;
            }
            $this->webhookMessageSender->sendError($webhookIntegration, $error);
            $this->webhookIntegrationWriteModel->save($webhookIntegration);
        }

        $telegramIntegrations = $this->telegramIntegrationReadModel->findAllByProjectId(Uuid::fromString($event->getProjectId()));

        /** @var TelegramIntegration $telegramIntegration */
        foreach ($telegramIntegrations as $telegramIntegration) {
            if ($telegramIntegration->getFilters()->isFiltered($error->getException()->getClass())) {
                continue;
            }
            $this->telegramMessageSender->sendError($telegramIntegration, $error);
            $this->telegramIntegrationWriteModel->save($telegramIntegration);
        }
    }
}