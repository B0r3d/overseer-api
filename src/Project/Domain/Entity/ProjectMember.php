<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;

class ProjectMember
{
    private Project $project;
    private string $id;
    private ProjectMemberId $_id;
    private ProjectMemberUsername $username;
    private \DateTime $joinedAt;

    public function __construct(ProjectMemberId $projectMemberId, Project $project, ProjectMemberUsername $projectMemberUsername)
    {
        $this->project = $project;
        $this->id = $projectMemberId->value();
        $this->_id = $projectMemberId;
        $this->username = $projectMemberUsername;
        $this->joinedAt = new \DateTime();
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getId(): ProjectMemberId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = ProjectMemberId::fromString($this->id);
        return $this->_id;
    }

    public function getUsername(): ProjectMemberUsername
    {
        return $this->username;
    }

    public function getJoinedAt(): \DateTime
    {
        return $this->joinedAt;
    }
}