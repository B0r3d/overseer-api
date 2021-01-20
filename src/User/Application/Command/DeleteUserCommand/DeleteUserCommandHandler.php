<?php


namespace Overseer\User\Application\Command\DeleteUserCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Command\DeleteUserCommand;
use Overseer\User\Domain\Event\UserDeleted;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\UserId;

class DeleteUserCommandHandler implements CommandHandler
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private DeleteUserCommandValidator $validator;
    private EventBus $eventBus;

    public function __construct(UserReadModel $userReadModel, UserWriteModel $userWriteModel, DeleteUserCommandValidator $validator, EventBus $eventBus)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->validator = $validator;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof DeleteUserCommand;
    }

    public function __invoke(DeleteUserCommand $command)
    {
        $this->validator->validate($command);

        $user = $this->userReadModel->findUser(UserId::fromString($command->getUserId()));
        $this->userWriteModel->delete($user);

        $this->eventBus->publish(new UserDeleted(
            $user->getId()->value(),
            $user->getUsername()->getValue()
        ));
    }
}