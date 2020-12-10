<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;

class ProjectMember
{
    private Project $project;
    private ?int $id;
    private ProjectMemberId $uuid;
    private ProjectMemberUsername $username;
    private \DateTime $joinedAt;

    public function __construct(ProjectMemberId $projectMemberId, Project $project, ProjectMemberUsername $projectMemberUsername)
    {
        $this->project = $project;
        $this->uuid = $projectMemberId;
        $this->username = $projectMemberUsername;
        $this->id = null;
        $this->joinedAt = new \DateTime();
    }

    public function project(): Project
    {
        return $this->project;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function uuid(): ProjectMemberId
    {
        return $this->uuid;
    }

    public function username(): ProjectMemberUsername
    {
        return $this->username;
    }

    public function joinedAt(): \DateTime
    {
        return $this->joinedAt;
    }
}