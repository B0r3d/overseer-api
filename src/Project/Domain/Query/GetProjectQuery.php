<?php


namespace Overseer\Project\Domain\Query;


use Overseer\Shared\Domain\ValueObject\SingleObjectQuery;

final class GetProjectQuery extends SingleObjectQuery
{
    private string $issuedBy;

    public function __construct(string $projectId, string $issuedBy)
    {
        $this->issuedBy = $issuedBy;

        parent::__construct($projectId);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }
}