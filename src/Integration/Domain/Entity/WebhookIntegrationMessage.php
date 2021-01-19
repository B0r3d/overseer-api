<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Shared\Domain\ValueObject\Uuid;

class WebhookIntegrationMessage extends IntegrationMessage
{
    private int $statusCode;
    private array $json;

    public function __construct(Integration $integration, Uuid $errorId, Uuid $id, array $json)
    {
        $this->statusCode = 0;
        $this->json = $json;
        parent::__construct($integration, $errorId, $id);
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getJson(): array
    {
        return $this->json;
    }
}