<?php


namespace Overseer\Project\Application;


use Overseer\Project\Domain\Entity\ApiKey;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Service\ApiKeyFactory;
use Overseer\Project\Domain\ValueObject\ApiKeyId;
use Overseer\Shared\Domain\Service\StringGenerator;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;

class SimpleApiKeyFactory implements ApiKeyFactory
{
    private StringGenerator $stringGenerator;

    public function __construct(StringGenerator $stringGenerator)
    {
        $this->stringGenerator = $stringGenerator;
    }

    public function createApiKey(Project $project, ?ExpiryDate $expiryDate = null): ApiKey
    {
        return new ApiKey(
            ApiKeyId::random(),
            $expiryDate,
            $this->stringGenerator->generate(64, 'pk_'),
            $project
        );
    }
}