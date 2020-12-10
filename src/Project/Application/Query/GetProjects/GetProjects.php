<?php


namespace Overseer\Project\Application\Query\GetProjects;


use Overseer\Shared\Domain\ValueObject\PaginatedQuery;

final class GetProjects extends PaginatedQuery
{
    private string $issuedBy;

    public function __construct(string $issuedBy, int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->issuedBy = $issuedBy;

        parent::__construct($page, $criteria, $sort);
    }

    public function issuedBy(): string
    {
        return $this->issuedBy;
    }
}