<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\WebhookIntegrationMessage;

class WebhookIntegrationMessageResource implements \JsonSerializable
{
    private string $id;
    private string $integrationId;
    private string $errorId;
    private string $status;
    private string $lastAttempt;
    private string $nextAttempt;
    private string $attemptCount;
    private string $statusCode;
    private string $response;
    private array $json;

    public function __construct(WebhookIntegrationMessage $message)
    {
        $this->id = $message->getId();
        $this->integrationId = $message->getIntegration()->getId()->value();
        $this->errorId = $message->getErrorId();
        $this->status = $message->getStatus();
        $this->lastAttempt = $message->getLastAttempt() ? $message->getLastAttempt()->format(\DateTime::ISO8601) : '';
        $this->nextAttempt = $message->getNextAttempt() ? $message->getNextAttempt()->format(\DateTime::ISO8601) : '';
        $this->attemptCount = $message->getAttemptCount();
        $this->statusCode = $message->getStatusCode();
        $this->response = $message->getResponse();
        $this->json = $message->getJson();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'integration_id' => $this->integrationId,
            'error_id' => $this->errorId,
            'status' => $this->status,
            'last_attempt' => $this->lastAttempt,
            'next_attempt' => $this->nextAttempt,
            'attempt_count' => $this->attemptCount,
            'status_code' => $this->statusCode,
            'response' => $this->response,
            'json' => $this->json,
        ];
    }
}