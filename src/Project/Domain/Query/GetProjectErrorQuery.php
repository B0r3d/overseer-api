<?php


namespace Overseer\Project\Domain\Query;


use Overseer\Shared\Domain\ValueObject\SingleObjectQuery;

final class GetProjectErrorQuery extends SingleObjectQuery
{
    private string $projectId;
    private string $issuedBy;

    public function __construct(string $projectId, string $errorId, string $issuedBy)
    {
        $this->projectId = $projectId;
        $this->issuedBy = $issuedBy;

        parent::__construct($errorId);
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