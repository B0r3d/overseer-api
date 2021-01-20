<?php


namespace Overseer\Integration\Domain\Query;


use Overseer\Shared\Domain\ValueObject\PaginatedQuery;

final class GetTelegramIntegrationsQuery extends PaginatedQuery
{
    private string $issuedBy;
    private string $projectId;

    public function __construct(string $issuedBy, string $projectId, int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;

        parent::__construct($page, $criteria, $sort);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}