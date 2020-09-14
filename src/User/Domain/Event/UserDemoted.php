<?php


namespace Overseer\User\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserDemoted extends DomainEvent
{

    static function eventName(): string
    {
        return 'user.demoted';
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