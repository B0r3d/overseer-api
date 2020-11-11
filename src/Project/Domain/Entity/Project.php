<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\Event\InvitationAccepted;
use Overseer\Project\Domain\Event\ProjectCreated;
use Overseer\Project\Domain\Event\ProjectMemberWasAdded;
use Overseer\Project\Domain\Event\UserInvitedToProject;
use Overseer\Project\Domain\Exception\InvitationAlreadySentException;
use Overseer\Project\Domain\Exception\UserAlreadyAProjectMemberException;
use Overseer\Project\Domain\ValueObject\Email;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;
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

    public function invite(Username $username, Email $email, ProjectMemberInvitationId $projectMemberInvitationId = null): void
    {
        if ($this->findInvitationWithUsername($username, new InvitationStatus(InvitationStatus::INVITED))) {
            throw InvitationAlreadySentException::withUsername($username);
        }

        if ($projectMemberInvitationId && $this->findInvitationWithId($projectMemberInvitationId)) {
            throw InvitationAlreadySentException::withUuid($projectMemberInvitationId);
        }

        if ($this->findMemberWithUsername($username)) {
            throw UserAlreadyAProjectMemberException::withUsername($username);
        }

        $invitationId = $projectMemberInvitationId ?? ProjectMemberInvitationId::random();
        $invitation = new ProjectMemberInvitation(
            $invitationId,
            $this,
            $username
        );

        $this->invitations[] = $invitation;
        $this->record(new UserInvitedToProject(
            $this->uuid,
            $email
        ));
    }

    public function findInvitationWithId(ProjectMemberInvitationId $invitationId, InvitationStatus $invitationStatus = null): ?ProjectMemberInvitation
    {
        /** @var ProjectMemberInvitation $invitation */
        foreach ($this->invitations as $invitation) {
            if ($invitationStatus && !$invitation->status()->equals($invitationStatus)) {
                continue;
            }

            if ($invitation->uuid()->equals($invitationId)) {
                return $invitation;
            }
        }

        return null;
    }

    public function findInvitationWithUsername(Username $username, InvitationStatus $invitationStatus = null): ?ProjectMemberInvitation
    {
        /** @var ProjectMemberInvitation $invitation */
        foreach ($this->invitations as $invitation) {
            if ($invitationStatus && !$invitation->status()->equals($invitationStatus)) {
                continue;
            }

            if ($invitation->username()->equals($username)) {
                return $invitation;
            }
        }

        return null;
    }

    public function acceptInvitation(ProjectMemberInvitation $invitation)
    {
        $invitation->accept();
        $this->record(new InvitationAccepted(
            $this->uuid,
            $invitation->username())
        );
    }

    public function addMember(ProjectMemberUsername $username, ProjectMemberId $projectMemberId = null)
    {
        if (!$projectMemberId) {
            $projectMemberId = ProjectMemberId::random();
        }

        $projectMember = new ProjectMember(
            $projectMemberId,
            $this,
            $username
        );

        $this->members[] = $projectMember;
        $this->record(new ProjectMemberWasAdded(
            $this->uuid,
            $username
        ));
    }

    private function findMemberWithUsername(Username $username): ?ProjectMember
    {
        /** @var ProjectMember $member */
        foreach ($this->members as $member) {
            if ($member->username()->equals($username)) {
                return $member;
            }
        }

        return null;
    }

    public function declineInvitation(ProjectMemberInvitation $invitation): void
    {
        $invitation->decline();
    }

    public function findMemberWithId(ProjectMemberId $projectMemberId): ?ProjectMember
    {
        /** @var ProjectMember $member */
        foreach($this->members as $member) {
            if ($member->uuid()->equals($projectMemberId)) {
                return $member;
            }
        }

        return null;
    }

    public function removeMember(ProjectMember $projectMember)
    {
        /** @var ProjectMember $member */
        foreach($this->members as $index => $member) {
            if ($member === $projectMember) {
                unset($this->members[$index]);
                break;
            }
        }
    }

    public function removeInvitation(ProjectMemberInvitation $invitation)
    {
        /** @var ProjectMemberInvitation $inv */
        foreach($this->invitations as $index => $inv) {
            if ($inv === $invitation) {
                unset($this->invitations[$index]);
                break;
            }
        }
    }
}