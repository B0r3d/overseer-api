<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;

class ProjectMemberInvitation
{
    private Project $project;
    private ?int $id;
    private ProjectMemberInvitationId $uuid;
    private Username $username;
    private InvitationStatus $status;
    private \DateTime $invitedAt;
    private ?\DateTime $respondedAt;

    public function __construct(
        ProjectMemberInvitationId $projectMemberInvitationId,
        Project $project,
        Username $username
    ) {
        $this->project = $project;
        $this->uuid = $projectMemberInvitationId;
        $this->username = $username;
        $this->id = null;
        $this->status = new InvitationStatus(InvitationStatus::INVITED);
        $this->invitedAt = new \DateTime();
        $this->respondedAt = null;
    }

    public function project(): Project
    {
        return $this->project;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function uuid(): ProjectMemberInvitationId
    {
        return $this->uuid;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function status(): InvitationStatus
    {
        return $this->status;
    }

    public function invitedAt(): \DateTime
    {
        return $this->invitedAt;
    }

    public function respondedAt(): ?\DateTime
    {
        return $this->respondedAt;
    }
}