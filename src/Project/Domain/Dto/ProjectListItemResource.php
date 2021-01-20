<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;

class ProjectListItemResource implements \JsonSerializable
{
    private string $id;
    private string $title;
    private string $slug;
    private ?string $description;
    private ?ProjectMemberInvitationResource $invitation;

    public function __construct(Project $project, ?ProjectMemberInvitation $invitation = null)
    {
        $this->id = $project->getId()->value();
        $this->title = $project->getProjectTitle()->getValue();
        $this->slug = $project->getSlug()->getValue();
        $this->description = $project->getDescription();
        $this->invitation = $invitation ? new ProjectMemberInvitationResource($invitation) : null;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'invitation' => $this->invitation,
        ];
    }
}