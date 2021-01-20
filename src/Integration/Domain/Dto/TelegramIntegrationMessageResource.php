<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\TelegramIntegrationMessage;

class TelegramIntegrationMessageResource implements \JsonSerializable
{
    private string $id;
    private string $integrationId;
    private string $errorId;
    private string $status;
    private string $lastAttempt;
    private string $nextAttempt;
    private string $attemptCount;
    private string $response;
    private string $telegramMessage;

    public function __construct(TelegramIntegrationMessage $message)
    {
        $this->id = $message->getId();
        $this->integrationId = $message->getIntegration()->getId()->value();
        $this->errorId = $message->getErrorId();
        $this->status = $message->getStatus();
        $this->lastAttempt = $message->getLastAttempt() ? $message->getLastAttempt()->format(\DateTime::ISO8601) : '';
        $this->nextAttempt = $message->getNextAttempt() ? $message->getNextAttempt()->format(\DateTime::ISO8601) : '';
        $this->attemptCount = $message->getAttemptCount();
        $this->response = $message->getResponse();
        $this->telegramMessage = $message->getTelegramMessage();
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
            'response' => $this->response,
            'telegram_message' => $this->telegramMessage,
        ];
    }
}