<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\ProjectMemberInvitation;

class ProjectMemberInvitationResource implements \JsonSerializable
{
    private string $id;
    private string $username;
    private string $status;
    private string $invitedAt;
    private ?string $respondedAt;

    public function __construct(ProjectMemberInvitation $invitation)
    {
        $this->id = $invitation->getId()->value();
        $this->username = $invitation->getUsername()->getValue();
        $this->status = $invitation->getStatus()->getValue();
        $this->invitedAt = $invitation->getInvitedAt()->format(\DateTime::ISO8601);
        $this->respondedAt = $invitation->getRespondedAt() ? $invitation->getRespondedAt()->format(\DateTime::ISO8601) : null;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'status' => $this->status,
            'invited_at' => $this->invitedAt,
            'responded_at' => $this->respondedAt
        ];
    }
}