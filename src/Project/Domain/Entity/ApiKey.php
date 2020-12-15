<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\ValueObject\ApiKeyId;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;

class ApiKey
{
    private ?string $id;
    private ApiKeyId $_id;
    private ?ExpiryDate $expiryDate;
    private \DateTime $createdAt;
    private string $value;
    private Project $project;

    public function __construct(ApiKeyId $id, ?ExpiryDate $expiryDate, string $value, Project $project)
    {
        $this->id = $id->value();
        $this->_id = $id;
        $this->expiryDate = $expiryDate;
        $this->value = $value;
        $this->project = $project;
        $this->createdAt = new \DateTime();
    }

    public function isExpired(): bool
    {
        if (!$this->expiryDate) {
            return false;
        }

        return $this->expiryDate->isExpired();
    }

    public function getId(): ApiKeyId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = ApiKeyId::fromString($this->id);
        return $this->_id;
    }

    public function getExpiryDate(): ?ExpiryDate
    {
        return $this->expiryDate;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}