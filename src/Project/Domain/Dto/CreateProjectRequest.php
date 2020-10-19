<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Shared\Domain\ValueObject\Uuid;

final class CreateProjectRequest
{
    private string $uuid;
    private string $projectTitle;
    private string $projectSlug;
    private ?string $description = null;

    public function uuid(): string
    {
        if (!isset($this->uuid)) {
            $this->uuid = Uuid::random()->value();
        }

        return $this->uuid;
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

    public function isValid()
    {
        if (!isset($this->projectTitle) || !$this->projectTitle) {
            return false;
        }

        // TODO: add slug validation
        if (!isset($this->projectSlug) || !$this->projectSlug) {
            return false;
        }

        return true;
    }
}