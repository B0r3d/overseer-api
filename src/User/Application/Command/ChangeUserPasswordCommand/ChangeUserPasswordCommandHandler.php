<?php


namespace Overseer\User\Application\Command\ChangeUserPasswordCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Command\ChangeUserPasswordCommand;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\PlainPassword;
use Overseer\User\Domain\ValueObject\UserId;

final class ChangeUserPasswordCommandHandler implements CommandHandler
{
    private ChangeUserPasswordCommandValidator $validator;
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private UserPasswordEncoder $userPasswordEncoder;
    private JWT $jwt;
    private EventBus $eventBus;

    public function __construct(ChangeUserPasswordCommandValidator $validator, UserReadModel $userReadModel, UserWriteModel $userWriteModel, UserPasswordEncoder $userPasswordEncoder, JWT $jwt, EventBus $eventBus)
    {
        $this->validator = $validator;
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->jwt = $jwt;
        $this->eventBus = $eventBus;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof ChangeUserPasswordCommand;
    }

    public function __invoke(ChangeUserPasswordCommand $command): void
    {
        $this->validator->validate($command);

        $userId = UserId::fromString($command->getUserId());
        $newPassword = new PlainPassword($command->getNewPassword());

        $user = $this->userReadModel->findUser($userId);

        try {
            $jwt = $this->jwt->decodeToken($command->getCurrentRefreshToken());
            $user->changePassword($newPassword, $this->userPasswordEncoder, $jwt);
        } catch(\Throwable $t) {
            $user->changePassword($newPassword, $this->userPasswordEncoder);
        }

        $events = $user->pullDomainEvents();
        $this->userWriteModel->save($user);

        $this->eventBus->publish(...$events);
    }
}