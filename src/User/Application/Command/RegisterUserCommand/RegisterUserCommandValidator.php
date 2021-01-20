<?php


namespace Overseer\User\Application\Command\RegisterUserCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\Email;
use Overseer\Shared\Domain\Validator\Specification\MinLength;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Overseer\User\Domain\Command\RegisterUserCommand;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Validator\UniqueEmail;
use Overseer\User\Domain\Validator\UniqueUsername;
use Overseer\User\Domain\Validator\ValidUsername;

final class RegisterUserCommandValidator implements CommandValidator
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof RegisterUserCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . RegisterUserCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getUserId(), 'Invalid id.', [
                new ValidUuid()
            ]),
            new Field($command->getUsername(), 'Invalid username', [
                new MinLength(4),
                new ValidUsername(),
                new UniqueUsername($this->userReadModel)
            ]),
            new Field($command->getEmail(), 'Invalid email', [
                new Email(),
                new UniqueEmail($this->userReadModel)
            ]),
            new Field($command->getPassword(), 'Invalid password', [
                new MinLength(4),
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}