<?php


namespace Overseer\Project\Application;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectResolver;

class ApiKeyProjectResolver implements ProjectResolver
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function resolve(string $apiKey): ?Project
    {
        $project = $this->projectReadModel->findByApiKey($apiKey);

        if (!$project) {
            return null;
        }

        $apiKey = $project->getApiKeys()->getApiKeyByValue($apiKey);

        if ($apiKey->isExpired()) {
            return null;
        }

        return $project;
    }
}