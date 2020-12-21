<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class RemoveProjectMemberCommand implements Command
{
    private string $projectMemberId;
    private string $issuedBy;
    private string $projectId;

    public function __construct(string $projectMemberId, string $issuedBy, string $projectId)
    {
        $this->projectMemberId = $projectMemberId;
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
    }

    public function getProjectMemberId(): string
    {
        return $this->projectMemberId;
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