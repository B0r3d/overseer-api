<?php


namespace Overseer\Project\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserInvitedToProject extends DomainEvent
{
    private string $userEmail;

    public function __construct(string $aggregateId, string $userEmail, string $eventId = null, string $occurredAt = null)
    {
        $this->userEmail = $userEmail;
        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    static function eventName(): string
    {
        return 'user.invited.to.project';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        $instance = new self($aggregateId, $eventId, $occurredAt);
        $instance->userEmail = $body['user_email'];
        return $instance;
    }

    public function toPrimitives(): array
    {
        return [

        ];
    }
}