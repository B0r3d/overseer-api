<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class AcceptInvitationCommand implements Command
{
    private string $invitationId;
    private string $acceptedBy;
    private string $projectId;

    public function __construct(string $invitationId, string $acceptedBy, string $projectId)
    {
        $this->invitationId = $invitationId;
        $this->acceptedBy = $acceptedBy;
        $this->projectId = $projectId;
    }

    public function getInvitationId(): string
    {
        return $this->invitationId;
    }

    public function getAcceptedBy(): string
    {
        return $this->acceptedBy;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}