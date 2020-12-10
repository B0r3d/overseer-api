<?php


namespace Overseer\Project\Application\Command\DeclineInvitation;


use Overseer\Project\Domain\Exception\ProjectMemberInvitationNotFoundException;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Exception\UnauthorizedException;

final class DeclineInvitationHandler implements CommandHandler
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
    }

    public function __invoke(DeclineInvitation $command): void
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

        $declinedBy = new Username($command->declinedBy());

        if (!$invitation->username()->equals($declinedBy)) {
            throw new UnauthorizedException();
        }

        $project->declineInvitation($invitation);
        $this->projectWriteModel->save($project);
    }
}