<?php


namespace Overseer\Project\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class InvitationAccepted extends DomainEvent
{
    private string $username;

    public function __construct(string $aggregateId, string $username, string $eventId = null, string $occurredAt = null)
    {
        $this->username = $username;
        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    static function eventName(): string
    {
        return 'invitation.accepted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        $username = $body['username'];
        return new self($aggregateId, $username, $eventId, $occurredAt);
    }

    public function toPrimitives(): array
    {
        return [

        ];
    }

    public function username(): string
    {
        return $this->username;
    }
}