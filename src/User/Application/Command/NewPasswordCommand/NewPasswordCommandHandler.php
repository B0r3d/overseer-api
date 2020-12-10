<?php


namespace Overseer\User\Application\Command\NewPasswordCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Command\NewPasswordCommand;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\PlainPassword;

final class NewPasswordCommandHandler implements CommandHandler
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private NewPasswordCommandValidator $validator;
    private UserPasswordEncoder $encoder;
    private EventBus $eventBus;

    public function __construct(NewPasswordCommandValidator $validator, UserReadModel $userReadModel, UserWriteModel $userWriteModel, UserPasswordEncoder $encoder, EventBus $eventBus)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->validator = $validator;
        $this->encoder = $encoder;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof NewPasswordCommand;
    }

    public function __invoke(NewPasswordCommand $command)
    {
        $this->validator->validate($command);

        $user = $this->userReadModel->findUserByPasswordResetToken($command->getPasswordResetToken());
        $plainPassword = new PlainPassword($command->getNewPassword());
        $user->changePassword($plainPassword, $this->encoder);

        $events = $user->pullDomainEvents();
        $this->userWriteModel->save($user);

        $this->eventBus->publish(...$events);
    }
}