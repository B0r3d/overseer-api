<?php


namespace Overseer\Project\Application\Command\AcceptInvitation;


use Overseer\Project\Domain\Exception\ProjectMemberInvitationNotFoundException;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\Exception\UnauthorizedException;

final class AcceptInvitationHandler implements CommandHandler
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


    public function __invoke(AcceptInvitation $command): void
    {
        $invitationId = ProjectMemberInvitationId::fromString($command->invitationId());
        $invitationStatus = new InvitationStatus(InvitationStatus::INVITED);

        $project = $this->projectReadModel->findByInvitationIdWithGivenStatus($invitationId, $invitationStatus);

        if (!$project) {
            throw ProjectNotFoundException::withProjectMemberInvitationId($command->invitationId());
        }

        $invitation = $project->findInvitationWithId($invitationId, $invitationStatus);

        if (!$invitation) {
            throw ProjectMemberInvitationNotFoundException::withUuid($invitationId);
        }

        $acceptedBy = new Username($command->acceptedBy());

        if (!$invitation->username()->equals($acceptedBy)) {
            throw new UnauthorizedException();
        }

        $project->acceptInvitation($invitation);
        $events  = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}