<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\ApiKey;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;

interface ApiKeyFactory
{
    public function createApiKey(Project $project, ?ExpiryDate $expiryDate = null): ApiKey;
}