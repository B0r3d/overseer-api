<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CancelInvitationCommand implements Command
{
    private string $invitationId;
    private string $issuedBy;
    private string $projectId;

    public function __construct(string $invitationId, string $issuedBy, string $projectId)
    {
        $this->invitationId = $invitationId;
        $this->issuedBy = $issuedBy;
        $this->projectId = $projectId;
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
}