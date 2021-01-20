<?php


namespace Overseer\Project\Domain\Query;

use Overseer\Shared\Domain\ValueObject\PaginatedQuery;

final class GetProjectsQuery extends PaginatedQuery
{
    private string $issuedBy;

    public function __construct(string $issuedBy, int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->issuedBy = $issuedBy;
        parent::__construct($page, $criteria, $sort);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }
}