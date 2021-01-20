<?php


namespace Overseer\User\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserDeleted extends DomainEvent
{
    private string $username;

    public function __construct(string $aggregateId, string $username, string $eventId = null, string $occurredAt = null)
    {
        $this->username = $username;

        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    static function eventName(): string
    {
        return 'user.deleted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self($aggregateId, $body['username'], $eventId, $occurredAt);
    }

    public function toPrimitives(): array
    {
        return [
            'username' => $this->username,
        ];
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}