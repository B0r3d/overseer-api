<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CreateProjectCommand implements Command
{
    private string $projectId;
    private string $projectTitle;
    private string $projectSlug;
    private string $description;
    private string $projectOwner;

    public function __construct(string $projectId, string $projectTitle, string $projectSlug, string $projectOwner, string $description = '')
    {
        $this->projectId = $projectId;
        $this->projectTitle = $projectTitle;
        $this->projectSlug = $projectSlug;
        $this->description = $description;
        $this->projectOwner = $projectOwner;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getProjectTitle(): string
    {
        return $this->projectTitle;
    }

    public function getProjectSlug(): string
    {
        return $this->projectSlug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getProjectOwner(): string
    {
        return $this->projectOwner;
    }
}