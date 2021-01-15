<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Ramsey\Uuid\Uuid;

class EventEntity
{
    private string $id;
    private string $aggregateId;
    private \DateTime $occurredAt;
    private array $payload;
    private string $status;
    private string $class;
    private string $errorMessage;
    private ?\DateTime $lastProcessDate;

    public function __construct(string $id, string $aggregateId, string $occurredAt, array $payload, string $class)
    {
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException('Invalid UUID');
        }

        if (!Uuid::isValid($aggregateId)) {
            throw new \InvalidArgumentException('Invalid aggregate UUID');
        }

        $occurredAt = new \DateTime('@' . $occurredAt);

        if (!$occurredAt) {
            throw new \InvalidArgumentException('Occurred at is not a valid timestamp');
        }

        $this->id = $id;
        $this->aggregateId = $aggregateId;
        $this->occurredAt = $occurredAt;
        $this->payload = $payload;
        $this->class = $class;
        $this->errorMessage = '';
        $this->lastProcessDate = null;
        $this->status = EventStatus::UNPROCESSED();
    }

    public function markAsProcessed()
    {
        $this->status = EventStatus::PROCESSED();
        $this->lastProcessDate = new \DateTime();
        $this->errorMessage = '';
    }

    public function markAsFailed(string $errorMessage)
    {
        $this->status = EventStatus::FAILED();
        $this->lastProcessDate = new \DateTime();
        $this->errorMessage = $errorMessage;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getLastProcessDate(): ?\DateTime
    {
        return $this->lastProcessDate;
    }
}