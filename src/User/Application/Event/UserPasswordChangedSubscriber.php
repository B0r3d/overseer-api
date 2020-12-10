<?php


namespace Overseer\User\Application\Event;


use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\User\Domain\Event\UserPasswordChanged;
use Overseer\User\Domain\Exception\UserNotFoundException;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\UserId;

final class UserPasswordChangedSubscriber implements EventSubscriber
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;

    public function __construct(UserReadModel $userReadModel, UserWriteModel $userWriteModel)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            UserPasswordChanged::class,
        ];
    }

    public function __invoke(UserPasswordChanged $event)
    {
        $userId = UserId::fromString($event->aggregateId());
        $user = $this->userReadModel->findUser($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->terminateSessions([
            $event->getCurrentUserSession()
        ]);

        $this->userWriteModel->save($user);
    }
}