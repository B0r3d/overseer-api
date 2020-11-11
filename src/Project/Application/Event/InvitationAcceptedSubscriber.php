<?php


namespace Overseer\Project\Application\Event;


use Overseer\Project\Domain\Event\InvitationAccepted;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;

final class InvitationAcceptedSubscriber implements EventSubscriber
{
    private ProjectReadModel $projectReadModel;
    private ProjectWriteModel $projectWriteModel;
//    Circular dependency issue
//    private EventBus $eventBus;

    public function __construct(ProjectReadModel $projectReadModel, ProjectWriteModel $projectWriteModel)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectWriteModel = $projectWriteModel;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            InvitationAccepted::class,
        ];
    }

    public function __invoke(InvitationAccepted $event)
    {
        $projectId = ProjectId::fromString($event->aggregateId());
        $project = $this->projectReadModel->findByUuid($projectId);

        if (!$project) {
            throw ProjectNotFoundException::withUuid($projectId);
        }

        $username = new ProjectMemberUsername($event->username());
        $project->addMember($username);

        $events = $project->pullDomainEvents();
        $this->projectWriteModel->save($project);

//        $this->eventBus->publish(...$events);
    }
}