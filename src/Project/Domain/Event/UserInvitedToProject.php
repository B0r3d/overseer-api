<?php


namespace Overseer\Project\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserInvitedToProject extends DomainEvent
{
    static function eventName(): string
    {
        return 'user.invited.to.project';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self($aggregateId, $eventId, $occurredAt);
    }

    public function toPrimitives(): array
    {
        return [

        ];
    }
}