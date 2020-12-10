<?php


namespace Overseer\Project\Application\Command\CreateProject;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CreateProject implements Command
{
    private string $projectId;
    private string $projectTitle;
    private string $projectSlug;
    private ?string $description;
    private string $projectOwner;

    public function __construct(string $projectId, string $projectTitle, string $projectSlug, string $projectOwner, string $description = null)
    {
        $this->projectId = $projectId;
        $this->projectTitle = $projectTitle;
        $this->projectSlug = $projectSlug;
        $this->description = $description;
        $this->projectOwner = $projectOwner;
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    public function projectTitle(): string
    {
        return $this->projectTitle;
    }

    public function projectSlug(): string
    {
        return $this->projectSlug;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function projectOwner(): string
    {
        return $this->projectOwner;
    }
}