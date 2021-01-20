<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Integration\Domain\Enum\MessageStatus;
use Overseer\Shared\Domain\ValueObject\Uuid;

abstract class IntegrationMessage
{
    protected Integration $integration;
    protected string $errorId;
    protected string $id;
    protected string $status;
    protected string $response;
    protected ?\DateTime $lastAttempt;
    protected int $attemptCount;
    protected ?\DateTime $nextAttempt;
    protected \DateTime $createdAt;

    public function __construct(Integration $integration, Uuid $errorId, Uuid $id)
    {
        $this->integration = $integration;
        $this->errorId = $errorId->value();
        $this->id = $id->value();
        $this->status = MessageStatus::UNPROCESSED();
        $this->response = '';
        $this->lastAttempt = null;
        $this->nextAttempt = null;
        $this->attemptCount = 1;
        $this->createdAt = new \DateTime();
    }

    public function markAsProcessed()
    {
        $this->status = MessageStatus::PROCESSED();
        $this->lastAttempt = new \DateTime();
        $this->response = '';
    }

    public function markAsFailed(string $response)
    {
        $this->response = $response;
        $this->status = MessageStatus::ERROR();
        $this->lastAttempt = new \DateTime();
    }

    public function scheduleNextAttempt()
    {
        $this->nextAttempt = new \DateTime();
        $this->nextAttempt->modify('+1hour');
    }

    public function increaseAttemptCount()
    {
        $this->attemptCount += 1;
    }

    public function getIntegration()
    {
        return $this->integration;
    }

    public function getErrorId(): string
    {
        return $this->errorId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getLastAttempt(): \DateTime
    {
        return $this->lastAttempt;
    }

    public function getAttemptCount(): int
    {
        return $this->attemptCount;
    }

    public function getNextAttempt(): ?\DateTime
    {
        return $this->nextAttempt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}