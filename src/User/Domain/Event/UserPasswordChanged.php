<?php


namespace Overseer\User\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserPasswordChanged extends DomainEvent
{
    private ?string $currentUserSession;

    static function eventName(): string
    {
        return 'user.password.changed';
    }

    public function __construct(string $aggregateId, ?string $currentUserSession = null, string $eventId = null, string $occurredAt = null)
    {
        $this->currentUserSession = $currentUserSession;
        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self($aggregateId, $body['current_user_session'], $eventId, $occurredAt);
    }

    public function toPrimitives(): array
    {
        return [

        ];
    }

    public function getCurrentUserSession(): ?string
    {
        return $this->currentUserSession;
    }
}