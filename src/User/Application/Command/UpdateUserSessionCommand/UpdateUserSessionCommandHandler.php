<?php


namespace Overseer\User\Application\Command\UpdateUserSessionCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\User\Domain\Command\UpdateUserSessionCommand;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;

final class UpdateUserSessionCommandHandler implements CommandHandler
{
    private UpdateUserSessionCommandValidator $validator;
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private JWT $jwt;

    public function __construct(UpdateUserSessionCommandValidator $validator, UserReadModel $userReadModel, UserWriteModel $userWriteModel, JWT $jwt)
    {
        $this->validator = $validator;
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->jwt = $jwt;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof UpdateUserSessionCommand;
    }

    public function __invoke(UpdateUserSessionCommand $command)
    {
        $this->validator->validate($command);

        $oldRefreshToken = $this->jwt->decodeToken($command->getOldRefreshToken());
        $newRefreshToken = $this->jwt->decodeToken($command->getNewRefreshToken());

        $user = $this->userReadModel->findUserByRefreshToken($oldRefreshToken);
        $sessions = $user->getSessions();
        $session = $sessions->findByRefreshToken($oldRefreshToken);

        $user->refreshSession($session, $newRefreshToken);

        $this->userWriteModel->save($user);
    }
}