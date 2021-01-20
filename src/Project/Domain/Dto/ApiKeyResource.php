<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\ApiKey;

class ApiKeyResource implements \JsonSerializable
{
    private string $id;
    private string $value;
    private string $createdAt;
    private ?string $expiryDate;

    public function __construct(ApiKey $apiKey)
    {
        $this->id = $apiKey->getId()->value();
        $this->value = $apiKey->getValue();
        $this->createdAt = $apiKey->getCreatedAt()->format(\DateTime::ISO8601);
        $this->expiryDate = $apiKey->getExpiryDate() && $apiKey->getExpiryDate()->getValue() ? $apiKey->getExpiryDate()->getValue()->format(\DateTime::ISO8601) : null;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'created_at' => $this->createdAt,
            'expiry_date' => $this->expiryDate,
        ];
    }
}