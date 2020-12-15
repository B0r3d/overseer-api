<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CreateApiKeyCommand implements Command
{
    private string $issuedBy;
    private ?string $expiryDate;
    private string $projectId;

    public function __construct(string $issuedBy, string $projectId, ?string $expiryDate = null)
    {
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
        $this->expiryDate = $expiryDate;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getExpiryDate(): ?string
    {
        return $this->expiryDate;
    }
}