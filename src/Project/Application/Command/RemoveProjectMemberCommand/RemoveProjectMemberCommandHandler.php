<?php


namespace Overseer\Project\Application\Command\RemoveProjectMemberCommand;


use Overseer\Project\Domain\Command\RemoveProjectMemberCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class RemoveProjectMemberCommandHandler implements CommandHandler
{
    private RemoveProjectMemberCommandValidator $validator;
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
    private EventBus $eventBus;

    public function __construct(RemoveProjectMemberCommandValidator $validator, ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof RemoveProjectMemberCommand;
    }

    public function __invoke(RemoveProjectMemberCommand $command): void
    {
        $this->validator->validate($command);

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));
        $member = $project->getMembers()->findMemberWithId(ProjectMemberId::fromString($command->getProjectMemberId()));

        $project->removeProjectMember($member);
        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

        $this->eventBus->publish(...$events);
    }
}