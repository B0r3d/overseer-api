<?php


namespace Overseer\Integration\Domain\Validator;


use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Validator\Specification;

class ValidWebhookIntegration implements Specification
{
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;

    public function __construct(WebhookIntegrationReadModel $webhookIntegrationReadModel)
    {
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $integration = $this->webhookIntegrationReadModel->findById(IntegrationId::fromString($value));
        return $integration !== null;
    }
}