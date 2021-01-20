<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\Collection\ApiKeys;
use Overseer\Project\Domain\Collection\Errors;
use Overseer\Project\Domain\Collection\Invitations;
use Overseer\Project\Domain\Collection\ProjectMembers;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Event\ErrorOccurred;
use Overseer\Project\Domain\Event\InvitationAccepted;
use Overseer\Project\Domain\Event\ProjectCreated;
use Overseer\Project\Domain\Event\ProjectMemberWasAdded;
use Overseer\Project\Domain\Event\UserInvitedToProject;
use Overseer\Project\Domain\Exception\InvitationAlreadySentException;
use Overseer\Project\Domain\Exception\UserAlreadyAProjectMemberException;
use Overseer\Project\Domain\ValueObject\ApiKeyId;
use Overseer\Project\Domain\ValueObject\Email;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;
use Overseer\Project\Domain\ValueObject\ProjectTitle;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Aggregate\AggregateRoot;

class Project extends AggregateRoot
{
    private string $id;
    private ProjectId $_id;
    private ProjectTitle $projectTitle;
    private ?string $description;
    private Slug $slug;
    private ?ProjectMember $projectOwner;
    private \DateTime $createdAt;
    private $invitations;
    private Invitations $_invitations;
    private $members;
    private ProjectMembers $_members;
    private $apiKeys;
    private ApiKeys $_apiKeys;
    private $errors;
    private Errors $_errors;

    public function getId(): ProjectId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = ProjectId::fromString($this->id);
        return $this->_id;
    }

    public function getProjectTitle(): ProjectTitle
    {
        return $this->projectTitle;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSlug(): Slug
    {
        return $this->slug;
    }

    public function getProjectOwner(): ProjectMember
    {
        return $this->projectOwner;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getInvitations(): Invitations
    {
        if (isset($this->_invitations)) {
            return $this->_invitations;
        }

        $this->_invitations = new Invitations(iterator_to_array($this->invitations));
        return $this->_invitations;
    }

    public function getMembers(): ProjectMembers
    {
        if (isset($this->_members)) {
            return $this->_members;
        }

        $this->_members = new ProjectMembers(iterator_to_array($this->members));
        return $this->_members;
    }

    public function getApiKeys(): ApiKeys
    {
        if (isset($this->_apiKeys)) {
            return $this->_apiKeys;
        }

        $this->_apiKeys = new ApiKeys(iterator_to_array($this->apiKeys));
        return $this->_apiKeys;
    }

    public function getErrors(): Errors
    {
        if (isset($this->_errors)) {
            return $this->_errors;
        }

        $this->_errors = new Errors(iterator_to_array($this->errors));
        return $this->_errors;
    }

    protected function __construct(ProjectId $uuid, ProjectTitle $projectTitle, Slug $slug, ProjectMemberUsername $projectOwner, string $description = null)
    {
        $this->id = $uuid->value();
        $this->projectTitle = $projectTitle;
        $this->slug = $slug;
        $this->description = $description;
        $this->_invitations = new Invitations();
        $this->_apiKeys = new ApiKeys();
        $this->_members = new ProjectMembers();
        $this->createdAt = new \DateTime();
        $this->_errors = new Errors();

        $projectOwner = new ProjectMember(
            ProjectMemberId::random(),
            $this,
            $projectOwner
        );

        $this->projectOwner = $projectOwner;
        $this->addMember($projectOwner);
    }

    public static function create(ProjectId $uuid, ProjectTitle $projectTitle, Slug $slug, ProjectMemberUsername $projectOwner, string $description = null): self
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

    public function invite(Username $username, Email $email, ProjectMemberInvitationId $projectMemberInvitationId): void
    {
        $invitations = $this->getInvitations();
        $invitation = $invitations->findInvitationWithUsername($username);

        if ($invitation && $invitation->getStatus()->equals(InvitationStatus::INVITED())) {
            throw InvitationAlreadySentException::withUsername($username);
        }

        $invitation = $invitations->findInvitationWithId($projectMemberInvitationId);
        if ($invitation) {
            throw InvitationAlreadySentException::withUuid($projectMemberInvitationId);
        }

        $members = $this->getMembers();
        if ($members->findMemberWithUsername($username)) {
            throw UserAlreadyAProjectMemberException::withUsername($username);
        }

        $invitationId = $projectMemberInvitationId ?? ProjectMemberInvitationId::random();
        $invitation = new ProjectMemberInvitation(
            $invitationId,
            $this,
            $username
        );

        $this->invitations[] = $invitation;
        $this->_invitations[] = $invitation;
        $this->record(new UserInvitedToProject(
            $this->id,
            $email,
            $invitationId
        ));
    }

    public function acceptInvitation(ProjectMemberInvitation $invitation)
    {
        $invitation->accept();
        $this->record(new InvitationAccepted(
            $this->id,
            $invitation->getUsername())
        );
    }

    public function addError(Error $error)
    {
        $this->errors[] = $error;
        $errors = $this->getErrors();
        $errors[] = $error;

        $this->record(new ErrorOccurred(
            $this->getId()->value(),
            $error->getId()->value()
        ));
    }

    public function addMember(ProjectMember $projectMember)
    {
        $members = $this->getMembers();
        $members[] = $projectMember;

        $this->_members = $members;
        $this->members[] = $projectMember;

        $this->record(new ProjectMemberWasAdded(
            $this->getId(),
            $projectMember->getUsername(),
        ));
    }

    public function removeProjectMember(ProjectMember $projectMember)
    {
        $members = $this->getMembers();
        $members->removeMember($projectMember);

        $this->_members = $members;
        $this->members->removeElement($projectMember);
    }

    public function declineInvitation(ProjectMemberInvitation $invitation): void
    {
        $invitation->decline();
    }

    public function removeInvitation(ProjectMemberInvitation $invitation)
    {
        $invitations = $this->getInvitations();
        $invitations->removeInvitation($invitation);

        $this->_invitations = $invitations;
        $this->invitations->removeElement($invitation);
    }

    public function addApiKey(ApiKey $apiKey): void
    {
        $apiKeys = $this->getApiKeys();
        $apiKeys[] = $apiKey;

        $this->apiKeys[] = $apiKey;
        $this->_apiKeys = $apiKeys;
    }

    public function getApiKey(ApiKeyId $apiKeyId): ?ApiKey
    {
        $apiKeys = $this->getApiKeys();
        return $apiKeys->getApiKey($apiKeyId);
    }

    public function removeApiKey(ApiKey $apiKey)
    {
        $apiKeys = $this->getApiKeys();
        $apiKeys->removeApiKey($apiKey);

        $this->apiKeys->removeElement($apiKey);
        $this->_apiKeys = $apiKeys;
    }

    public function isProjectOwner(Username $username): bool
    {
        return $this->projectOwner->getUsername()->equals($username);
    }

    public function changeProjectOwner(ProjectMember $newProjectOwner)
    {
        $this->projectOwner = $newProjectOwner;
    }

    public function preDelete()
    {
        $this->projectOwner = null;
    }
}