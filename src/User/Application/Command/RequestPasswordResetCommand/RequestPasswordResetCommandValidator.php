<?php


namespace Overseer\User\Application\Command\RequestPasswordResetCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Overseer\User\Domain\Command\RequestPasswordResetCommand;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Validator\ValidUser;

final class RequestPasswordResetCommandValidator implements CommandValidator
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof RequestPasswordResetCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . RequestPasswordResetCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getLogin(), 'User does not exist.', [
                new ValidUser($this->userReadModel)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}