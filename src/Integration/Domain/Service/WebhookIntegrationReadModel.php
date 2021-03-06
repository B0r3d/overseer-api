<?php


namespace Overseer\Integration\Domain\Service;


use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

interface WebhookIntegrationReadModel
{
    public function findById(IntegrationId $id): ?WebhookIntegration;
    public function findAllByProjectId(Uuid $projectId): array;
    public function findUnprocessedMessages(): array;
    public function findUnprocessedMessagesCount(): int;
    public function findFailedMessages(): array;
    public function findFailedMessagesCount(): int;
    public function findIntegrations(Uuid $projectId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function findIntegrationsCount(Uuid $projectId, array $criteria = []): int;
    public function findMessages(IntegrationId $integrationId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function findMessagesCount(IntegrationId $integrationId, array $criteria = []);
}