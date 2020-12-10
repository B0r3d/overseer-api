<?php


namespace Overseer\User\Application\Command\UpdateUserSessionCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Overseer\User\Domain\Command\UpdateUserSessionCommand;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Validator\RefreshTokenNotTerminated;
use Overseer\User\Domain\Validator\ValidRefreshToken;
use Overseer\User\Domain\Validator\ValidUser;

final class UpdateUserSessionCommandValidator implements CommandValidator
{
    private UserReadModel $userReadModel;
    private JWT $jwt;

    public function __construct(UserReadModel $userReadModel, JWT $jwt)
    {
        $this->userReadModel = $userReadModel;
        $this->jwt = $jwt;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof UpdateUserSessionCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . UpdateUserSessionCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getUsername(), 'Invalid user provided.', [
                new ValidUser($this->userReadModel),
            ]),
            new Field($command->getOldRefreshToken(), 'Refresh token is invalid', [
                new ValidRefreshToken($this->jwt),
                new RefreshTokenNotTerminated($this->userReadModel, $this->jwt),
            ]),
            new Field($command->getNewRefreshToken(), 'New refresh token is invalid', [
                new ValidRefreshToken($this->jwt),
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}