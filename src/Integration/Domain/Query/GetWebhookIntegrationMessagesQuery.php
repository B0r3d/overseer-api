<?php


namespace Overseer\Integration\Domain\Query;


use Overseer\Shared\Domain\ValueObject\PaginatedQuery;

final class GetWebhookIntegrationMessagesQuery extends PaginatedQuery
{
    private string $issuedBy;
    private string $integrationId;

    public function __construct(string $issuedBy, string $integrationId, int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->issuedBy = $issuedBy;
        $this->integrationId = $integrationId;

        parent::__construct($page, $criteria, $sort);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getIntegrationId(): string
    {
        return $this->integrationId;
    }
}