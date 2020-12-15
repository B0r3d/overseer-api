<?php


namespace Overseer\Project\Domain\Collection;


use Overseer\Project\Domain\Entity\ApiKey;
use Overseer\Project\Domain\ValueObject\ApiKeyId;

class ApiKeys extends \ArrayObject
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);
    }

    public function getApiKey(ApiKeyId $apiKeyId): ?ApiKey
    {
        /** @var ApiKey $apiKey */
        foreach ($this as $apiKey) {
            if ($apiKey->getId()->equals($apiKeyId)) {
                return $apiKey;
            }
        }

        return null;
    }

    public function removeApiKey(ApiKey $apiKey)
    {
        /** @var ApiKey $key */
        foreach ($this as $index => $key) {
            if ($key->getId()->equals($apiKey->getId())) {
                unset($this[$index]);
                break;
            }
        }
    }
}