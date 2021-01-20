<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class InviteMemberCommand implements Command
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

    public function getInvitationId(): string
    {
        return $this->invitationId;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}