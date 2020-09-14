<?php


namespace Overseer\User\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserPromoted extends DomainEvent
{
    public function __construct(string $aggregateId, string $eventId = null, string $occurredAt = null)
    {
        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    static function eventName(): string
    {
        return 'user.promoted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self($aggregateId, $eventId, $occurredAt);
    }

    public function toPrimitives(): array
    {
        return [];
    }
}