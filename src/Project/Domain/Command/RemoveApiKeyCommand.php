<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class RemoveApiKeyCommand implements Command
{
    private string $issuedBy;
    private string $apiKeyId;
    private string $projectId;

    public function __construct(string $issuedBy, string $apiKeyId, string $projectId)
    {
        $this->issuedBy = $issuedBy;
        $this->apiKeyId = $apiKeyId;
        $this->projectId = $projectId;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getApiKeyId(): string
    {
        return $this->apiKeyId;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}