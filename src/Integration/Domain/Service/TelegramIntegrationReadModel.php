<?php


namespace Overseer\Integration\Domain\Service;


use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

interface TelegramIntegrationReadModel
{
    public function findById(IntegrationId $id): ?TelegramIntegration;
    public function findAllByProjectId(Uuid $projectId): array;
    public function findIntegrations(Uuid $projectId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function findIntegrationsCount(Uuid $projectId, array $criteria = []): int;
    public function findMessages(IntegrationId $integrationId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function findMessagesCount(IntegrationId $integrationId, array $criteria = []): int;
    public function findUnprocessedMessages(): array;
    public function findUnprocessedMessagesCount(): int;
    public function findFailedMessages(): array;
    public function findFailedMessagesCount(): int;
}