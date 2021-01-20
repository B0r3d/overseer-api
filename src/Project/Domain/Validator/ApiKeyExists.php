<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ApiKeyId;
use Overseer\Shared\Domain\Validator\Specification;

class ApiKeyExists implements Specification
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function isSatisfiedBy($value): bool
    {
        $apiKeyId = ApiKeyId::fromString($value);
        $apiKey = $this->project->getApiKey($apiKeyId);
        return $apiKey !== null;
    }
}