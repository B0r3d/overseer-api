<?php


namespace Overseer\Project\Application\Command\CancelInvitationCommand;


use Overseer\Project\Domain\Command\CancelInvitationCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class CancelInvitationCommandHandler implements CommandHandler
{
    private CancelInvitationCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(CancelInvitationCommandValidator $validator, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof CancelInvitationCommand;
    }

    public function __invoke(CancelInvitationCommand $command)
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));
        $invitation = $project->getInvitations()->findInvitationWithId(ProjectMemberInvitationId::fromString($command->getInvitationId()));

        $project->removeInvitation($invitation);
        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}