<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\Project;

class ProjectResource implements \JsonSerializable
{
    private string $id;
    private string $title;
    private ?string $description;
    private ProjectMemberResource $projectOwner;
    private string $createdAt;
    private array $projectMembers;
    private array $invitations;
    private array $apiKeys;

    public function __construct(Project $project)
    {
        $this->id = $project->getId()->value();
        $this->title = $project->getProjectTitle()->getValue();
        $this->description = $project->getDescription();
        $this->projectOwner = new ProjectMemberResource($project->getProjectOwner());
        $this->createdAt = $project->getCreatedAt()->format(\DateTime::ISO8601);

        $this->projectMembers = [];
        $this->invitations = [];
        $this->apiKeys = [];
    }

    public function addProjectMember(ProjectMemberResource $resource)
    {
        $this->projectMembers[] = $resource;
    }

    public function addInvitation(ProjectMemberInvitationResource $resource)
    {
        $this->invitations[] = $resource;
    }

    public function addApiKey(ApiKeyResource $resource)
    {
        $this->apiKeys[] = $resource;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'project_owner' => $this->projectOwner,
            'created_at' => $this->createdAt,
            'project_members' => $this->projectMembers,
            'invitations' => $this->invitations,
            'api_keys' => $this->apiKeys
        ];
    }
}