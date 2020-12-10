<?php


namespace Overseer\Project\Application\Command\InviteMember;


use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\Email;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\Exception\UnauthorizedException;

final class InviteMemberHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function __invoke(InviteMember $command): void
    {
        $projectId = ProjectId::fromString($command->projectId());
        $project = $this->projectReadModel->findByUuid($projectId);

        if (!$project) {
            throw ProjectNotFoundException::withUuid($projectId);
        }

        $issuedBy = new ProjectOwner($command->issuedBy());
        if (!$project->projectOwner()->equals($issuedBy)) {
            throw new UnauthorizedException();
        }

        $username = new Username($command->username());
        $email = new Email($command->email());
        $projectMemberInvitationId = ProjectMemberInvitationId::fromString($command->invitationId());

        $project->invite($username, $email, $projectMemberInvitationId);

        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}