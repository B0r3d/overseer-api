<?php


namespace Overseer\User\Application\Command\InvalidateRefreshTokenCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\User\Domain\Command\InvalidateRefreshTokenCommand;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\JsonWebToken;

final class InvalidateRefreshTokenCommandHandler implements CommandHandler
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;

    public function __construct(UserReadModel $userReadModel, UserWriteModel $userWriteModel)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
    }

    public function handles(Command $command): bool
    {
        return $command instanceof InvalidateRefreshTokenCommand;
    }

    public function __invoke(InvalidateRefreshTokenCommand $command)
    {
        $refreshToken = new JsonWebToken($command->getRefreshToken(), []);
        $user = $this->userReadModel->findUserByRefreshToken($refreshToken);
        $sessions = $user->getSessions();
        $session = $sessions->findByRefreshToken($refreshToken);

        if (!$session) {
            return;
        }

        $user->invalidateSession($session);
        $this->userWriteModel->save($user);
    }
}