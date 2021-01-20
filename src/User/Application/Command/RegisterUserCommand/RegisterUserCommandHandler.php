<?php


namespace Overseer\User\Application\Command\RegisterUserCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Command\RegisterUserCommand;
use Overseer\User\Domain\Service\UserFactory;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\Password;
use Overseer\User\Domain\ValueObject\PlainPassword;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

final class RegisterUserCommandHandler implements CommandHandler
{
    private CommandValidator $commandValidator;
    private UserFactory $userFactory;
    private UserWriteModel $userWriteModel;
    private EventBus $eventBus;

    public function __construct(CommandValidator $commandValidator, UserFactory $userFactory, UserWriteModel $userWriteModel, EventBus $eventBus)
    {
        $this->commandValidator = $commandValidator;
        $this->userFactory = $userFactory;
        $this->userWriteModel = $userWriteModel;
        $this->eventBus = $eventBus;
    }


    public function handles(Command $command): bool
    {
        return $command instanceof RegisterUserCommand;
    }

    public function __invoke(RegisterUserCommand $command)
    {
        $this->commandValidator->validate($command);

        $user = $this->userFactory->createUser(
            UserId::fromString($command->getUserId()),
            new Username($command->getUsername()),
            new Email($command->getEmail()),
            new PlainPassword($command->getPassword())
        );

        $this->userWriteModel->save($user);
        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}