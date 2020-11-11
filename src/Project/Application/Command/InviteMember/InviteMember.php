<?php


namespace Overseer\Project\Application\Command\InviteMember;


use Overseer\Shared\Domain\Bus\Command\Command;

final class InviteMember implements Command
{
    private string $invitationId;
    private string $issuedBy;
    private string $projectId;
    private string $username;
    private string $email;

    public function __construct(string $invitationId, string $issuedBy, string $projectId, string $username, string $email)
    {
        $this->invitationId = $invitationId;
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
        $this->username = $username;
        $this->email = $email;
    }

    public function invitationId(): string
    {
        return $this->invitationId;
    }

    public function issuedBy(): string
    {
        return $this->issuedBy;
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }
}