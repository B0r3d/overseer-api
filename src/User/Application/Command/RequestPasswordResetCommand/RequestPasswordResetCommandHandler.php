<?php


namespace Overseer\User\Application\Command\RequestPasswordResetCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Command\RequestPasswordResetCommand;
use Overseer\User\Domain\Service\PasswordResetTokenFactory;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;

final class RequestPasswordResetCommandHandler implements CommandHandler
{
    private RequestPasswordResetCommandValidator $validator;
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private PasswordResetTokenFactory $passwordResetTokenFactory;
    private EventBus $eventBus;

    public function __construct(RequestPasswordResetCommandValidator $validator, UserReadModel $userReadModel, UserWriteModel $userWriteModel, PasswordResetTokenFactory $passwordResetTokenFactory, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->passwordResetTokenFactory = $passwordResetTokenFactory;
        $this->eventBus = $eventBus;
    }


    public function handles(Command $command): bool
    {
        return $command instanceof RequestPasswordResetCommand;
    }

    public function __invoke(RequestPasswordResetCommand $command): void
    {
        $this->validator->validate($command);

        $user = $this->userReadModel->findOneByLogin($command->getLogin());
        $user->requestPasswordReset($this->passwordResetTokenFactory->createToken());
        $events = $user->pullDomainEvents();
        $this->userWriteModel->save($user);

        $this->eventBus->publish(...$events);
    }
}