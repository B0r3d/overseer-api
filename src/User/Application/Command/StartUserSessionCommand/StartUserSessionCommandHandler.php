<?php


namespace Overseer\User\Application\Command\StartUserSessionCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\Command\StartUserSessionCommand;
use Overseer\User\Domain\Exception\UserNotFoundException;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\SessionId;
use Overseer\User\Domain\ValueObject\UserId;

final class StartUserSessionCommandHandler implements CommandHandler
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
        return $command instanceof StartUserSessionCommand;
    }

    public function __invoke(StartUserSessionCommand $command)
    {
        $userId = UserId::fromString($command->getUserId());
        $user = $this->userReadModel->findUser($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $sessionId = SessionId::fromString($command->getSessionId());
        $expiryDate = new ExpiryDate((new \DateTime())->setTimestamp($command->getRefreshTokenExpiryTimestamp()));
        $sessionStart = (new \DateTime())->setTimestamp($command->getRefreshTokenIssuedAtTimestamp());

        $user->startSession(
            $sessionId,
            $expiryDate,
            $sessionStart,
            $command->getRefreshToken()
        );

        $this->userWriteModel->save($user);
    }
}