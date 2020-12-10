<?php


namespace Overseer\User\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class UserPasswordResetRequested extends DomainEvent
{
    private string $email;
    private string $passwordResetTokenId;

    public function __construct(string $aggregateId, string $email, string $passwordResetTokenId, string $eventId = null, string $occurredAt = null)
    {
        $this->email = $email;
        $this->passwordResetTokenId = $passwordResetTokenId;
        parent::__construct($aggregateId, $eventId, $occurredAt);
    }

    static function eventName(): string
    {
        return 'user.password.reset.requested';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self(
            $aggregateId,
            $body['email'],
            $body['password_reset_token'],
            $eventId,
            $occurredAt
        );
    }

    public function toPrimitives(): array
    {
        return [

        ];
    }
}