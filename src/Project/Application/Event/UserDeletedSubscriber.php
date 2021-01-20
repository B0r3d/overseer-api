<?php


namespace Overseer\Project\Application\Event;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Event\ProjectDeleted;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Service\ProjectWriteModel;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\User\Domain\Event\UserDeleted;

final class UserDeletedSubscriber implements EventSubscriber
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

    static function getSubscribedDomainEvents(): array
    {
        return [
            UserDeleted::class,
        ];
    }

    public function __invoke(UserDeleted $event)
    {
        $projects = $this->projectReadModel->findWhereUserIsAMember(new Username($event->getUsername()));
        $events = [];

        /** @var Project $project */
        foreach ($projects as $project) {
            $members = $project->getMembers();
            if (count($members) > 1) {
                $member = $members->findMemberWithUsername(new Username($event->getUsername()));
                $members->removeMember($member);
                $project->changeProjectOwner($members[0]);
                $this->projectWriteModel->save($project);
            } else {
                $this->projectWriteModel->delete($project);
                $events[] = new ProjectDeleted($project->getId()->value());
            }
        }

        $this->eventBus->publish(...$events);
    }
}