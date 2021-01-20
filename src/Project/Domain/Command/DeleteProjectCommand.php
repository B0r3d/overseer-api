<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class DeleteProjectCommand implements Command
{
    private string $issuedBy;
    private string $projectId;

    public function __construct(string $issuedBy, string $projectId)
    {
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
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