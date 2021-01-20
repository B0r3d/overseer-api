<?php


namespace Overseer\Project\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserInvitedToProject extends DomainEvent
{
    private string $userEmail;
    private string $invitationId;

    public function __construct(string $aggregateId, string $userEmail, string $invitationId, string $eventId = null, string $occurredAt = null)
    {
        $this->userEmail = $userEmail;
        $this->invitationId = $invitationId;

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
        $instance->invitationId = $body['invitation_id'];
        return $instance;
    }

    public function toPrimitives(): array
    {
        return [
            'user_email' => $this->userEmail,
            'invitation_id' => $this->invitationId,
        ];
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getInvitationId(): string
    {
        return $this->invitationId;
    }
}