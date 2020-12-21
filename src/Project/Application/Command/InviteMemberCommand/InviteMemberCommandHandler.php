<?php


namespace Overseer\Project\Application\Command\InviteMemberCommand;


use Overseer\Project\Domain\Command\InviteMemberCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\Email;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class InviteMemberCommandHandler implements CommandHandler
{
    private InviteMemberCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(InviteMemberCommandValidator $validator, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof InviteMemberCommand;
    }

    public function __invoke(InviteMemberCommand $command): void
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        $username = new Username($command->getUsername());
        $email = new Email($command->getEmail());
        $projectMemberInvitationId = ProjectMemberInvitationId::fromString($command->getInvitationId());

        $project->invite($username, $email, $projectMemberInvitationId);

        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}