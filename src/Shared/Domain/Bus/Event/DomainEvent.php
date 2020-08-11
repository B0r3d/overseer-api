<?php


namespace Overseer\Shared\Domain\Bus\Event;


use Overseer\Shared\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    private string $aggregateId;
    private string $eventId;
    private string $occurredAt;

    public function __construct(string $aggregateId, string $eventId = null, string $occurredAt = null)
    {
        $this->aggregateId = $aggregateId;
        $this->eventId     = $eventId ?? Uuid::random()->value();
        $this->occurredAt  = $occurredAt ?? (new \DateTimeImmutable())->getTimestamp();
    }

    abstract static function eventName(): string;

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredAt
    ): self;

    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }
}