<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class DeclineInvitationCommand implements Command
{
    private string $invitationId;
    private string $declinedBy;
    private string $projectId;

    public function __construct(string $invitationId, string $declinedBy, string $projectId)
    {
        $this->invitationId = $invitationId;
        $this->declinedBy = $declinedBy;
        $this->projectId = $projectId;
    }

    public function getInvitationId(): string
    {
        return $this->invitationId;
    }

    public function getDeclinedBy(): string
    {
        return $this->declinedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}