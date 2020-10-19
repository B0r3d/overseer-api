<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\Event\ProjectCreated;
use Overseer\Project\Domain\Event\UserInvitedToProject;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Project\Domain\ValueObject\ProjectTitle;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Aggregate\AggregateRoot;

class Project extends AggregateRoot
{
    private ?int $id;
    private ProjectId $uuid;
    private ProjectTitle $projectTitle;
    private ?string $description;
    private Slug $slug;
    private ProjectOwner $projectOwner;
    private \DateTime $createdAt;
    private iterable $invitations;
    private iterable $members;

    public function id(): ?int
    {
        return $this->id;
    }

    public function uuid(): ProjectId
    {
        return $this->uuid;
    }

    public function projectTitle(): ProjectTitle
    {
        return $this->projectTitle;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function projectOwner(): ProjectOwner
    {
        return $this->projectOwner;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function invitations(): iterable
    {
        return $this->invitations;
    }

    public function members(): iterable
    {
        return $this->members;
    }

    protected function __construct(ProjectId $uuid, ProjectTitle $projectTitle, Slug $slug, ProjectOwner $projectOwner, string $description = null)
    {
        $this->uuid = $uuid;
        $this->projectTitle = $projectTitle;
        $this->slug = $slug;
        $this->projectOwner = $projectOwner;
        $this->description = $description;
        $this->id = null;
        $this->createdAt = new \DateTime();
    }

    public static function create(ProjectId $uuid, ProjectTitle $projectTitle, Slug $slug, ProjectOwner $projectOwner, string $description = null): self
    {
        $instance = new self(
            $uuid,
            $projectTitle,
            $slug,
            $projectOwner,
            $description
        );

        $instance->record(new ProjectCreated($uuid));

        return $instance;
    }

    public function rename(ProjectTitle $newTitle): void
    {
        $this->projectTitle = $newTitle;
    }

    public function invite(Username $username, ProjectMemberInvitationId $projectMemberInvitationId = null): void
    {
        $invitationId = $projectMemberInvitationId ?? ProjectMemberInvitationId::random();
        $invitation = new ProjectMemberInvitation(
            $invitationId,
            $this,
            $username
        );

        $this->invitations[] = $invitation;
        $this->record(new UserInvitedToProject(
            $this->uuid,
        ));
    }
}