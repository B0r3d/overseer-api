<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\ProjectMember;

class ProjectMemberResource implements \JsonSerializable
{
    private string $id;
    private string $username;
    private string $joinedAt;

    public function __construct(ProjectMember $projectMember)
    {
        $this->id = $projectMember->getId()->value();
        $this->username = $projectMember->getUsername()->getValue();
        $this->joinedAt = $projectMember->getJoinedAt()->format(\DateTime::ISO8601);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'joined_at' => $this->joinedAt
        ];
    }
}