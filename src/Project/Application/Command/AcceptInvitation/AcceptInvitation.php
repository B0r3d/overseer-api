<?php


namespace Overseer\Project\Application\Command\AcceptInvitation;


use Overseer\Shared\Domain\Bus\Command\Command;

final class AcceptInvitation implements Command
{
    private string $invitationId;
    private string $acceptedBy;

    public function __construct(string $invitationId, string $acceptedBy)
    {
        $this->invitationId = $invitationId;
        $this->acceptedBy = $acceptedBy;
    }

    public function invitationId(): string
    {
        return $this->invitationId;
    }

    public function acceptedBy(): string
    {
        return $this->acceptedBy;
    }
}