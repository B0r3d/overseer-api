<?php


namespace Overseer\Project\Domain\Query;


use Overseer\Shared\Domain\ValueObject\SingleObjectQuery;

class GetProjectErrorsSummaryQuery extends SingleObjectQuery
{
    private string $issuedBy;
    private array $criteria;

    public function __construct(string $projectId, string $issuedBy, array $criteria = [])
    {
        $this->issuedBy = $issuedBy;
        $this->criteria = $criteria;
        parent::__construct($projectId);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }
}