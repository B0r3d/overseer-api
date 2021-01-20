<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class UpdateProjectCommand implements Command
{
    private string $issuedBy;
    private string $projectId;
    private string $description;

    public function __construct(string $issuedBy, string $projectId, string $description)
    {
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
        $this->description = $description;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}