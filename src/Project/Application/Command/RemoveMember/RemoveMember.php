<?php


namespace Overseer\Project\Application\Command\RemoveMember;


use Overseer\Shared\Domain\Bus\Command\Command;

final class RemoveMember implements Command
{
    private string $projectMemberId;
    private string $issuedBy;

    public function __construct(string $projectMemberId, string $issuedBy)
    {
        $this->projectMemberId = $projectMemberId;
        $this->issuedBy = $issuedBy;
    }

    public function projectMemberId(): string
    {
        return $this->projectMemberId;
    }

    public function issuedBy(): string
    {
        return $this->issuedBy;
    }
}