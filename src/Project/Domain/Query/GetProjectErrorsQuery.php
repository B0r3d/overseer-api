<?php


namespace Overseer\Project\Domain\Query;


use Overseer\Shared\Domain\ValueObject\PaginatedQuery;

final class GetProjectErrorsQuery extends PaginatedQuery
{
    private string $projectId;
    private string $issuedBy;

    public function __construct(string $projectId, string $issuedBy, int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->projectId = $projectId;
        $this->issuedBy = $issuedBy;

        parent::__construct($page, $criteria, $sort);
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }
}