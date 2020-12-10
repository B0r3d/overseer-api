<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Shared\Domain\ValueObject\Uuid;

final class InviteNewMemberRequest
{
    private string $invitationId;
    private string $username;
    private string $email;

    public function __construct(string $invitationId, string $username, string $email)
    {
        $this->invitationId = $invitationId;
        $this->username = $username;
        $this->email = $email;
    }

    public function invitationId(): string
    {
        if (!isset($this->invitationId)) {
            $this->invitationId = Uuid::random()->value();
        }

        return $this->invitationId;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function isValid(): bool
    {
        if (!isset($this->username) || !$this->username) {
            return false;
        }

        if (!isset($this->email) || !$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (isset($this->invitationId) && !\Ramsey\Uuid\Uuid::isValid($this->invitationId)) {
            return false;
        }

        return true;
    }
}