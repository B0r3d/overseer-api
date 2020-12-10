<?php


namespace Overseer\Project\Application\Command\DeclineInvitation;


use Overseer\Shared\Domain\Bus\Command\Command;

final class DeclineInvitation implements Command
{
    private string $invitationId;
    private string $declinedBy;

    public function __construct(string $invitationId, string $declinedBy)
    {
        $this->invitationId = $invitationId;
        $this->declinedBy = $declinedBy;
    }

    public function invitationId(): string
    {
        return $this->invitationId;
    }

    public function declinedBy(): string
    {
        return $this->declinedBy;
    }
}