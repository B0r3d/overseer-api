<?php


namespace Overseer\Project\Application\Command\RemoveInvitation;


use Overseer\Shared\Domain\Bus\Command\Command;

final class RemoveInvitation implements Command
{
    private string $invitationId;
    private string $issuedBy;

    public function __construct(string $invitationId, string $issuedBy)
    {
        $this->invitationId = $invitationId;
        $this->issuedBy = $issuedBy;
    }

    public function invitationId(): string
    {
        return $this->invitationId;
    }

    public function issuedBy(): string
    {
        return $this->issuedBy;
    }
}