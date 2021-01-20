<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;

class ProjectMemberInvitation
{
    private Project $project;
    private string $id;
    private ProjectMemberInvitationId $_id;
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
        $this->id = $projectMemberInvitationId->value();
        $this->_id = $projectMemberInvitationId;
        $this->username = $username;
        $this->status = InvitationStatus::INVITED();
        $this->invitedAt = new \DateTime();
        $this->respondedAt = null;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getId(): ProjectMemberInvitationId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = ProjectMemberInvitationId::fromString($this->id);
        return $this->_id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getStatus(): InvitationStatus
    {
        return $this->status;
    }

    public function getInvitedAt(): \DateTime
    {
        return $this->invitedAt;
    }

    public function getRespondedAt(): ?\DateTime
    {
        return $this->respondedAt;
    }

    public function accept()
    {
        $this->status = InvitationStatus::ACCEPTED();
        $this->respondedAt = new \DateTime();
    }

    public function decline()
    {
        $this->status = InvitationStatus::DECLINED();
        $this->respondedAt = new \DateTime();
    }
}