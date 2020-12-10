<?php


namespace Overseer\User\Application\Command\ChangeUserPasswordCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\MinLength;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Overseer\User\Domain\Command\ChangeUserPasswordCommand;
use Overseer\User\Domain\Exception\UserNotFoundException;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Validator\ValidPassword;
use Overseer\User\Domain\ValueObject\UserId;

final class ChangeUserPasswordCommandValidator implements CommandValidator
{
    private UserReadModel $userReadModel;
    private UserPasswordEncoder $passwordEncoder;

    public function __construct(UserReadModel $userReadModel, UserPasswordEncoder $passwordEncoder)
    {
        $this->userReadModel = $userReadModel;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof ChangeUserPasswordCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . ChangeUserPasswordCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getUserId(), 'User does not exist.', [
                new ValidUuid()
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new UserNotFoundException($validationContext->getErrorMessage());
        }

        $user = $this->userReadModel->findUser(UserId::fromString($command->getUserId()));
        if (!$user) {
            throw new UserNotFoundException('User does not exist.');
        }

        $validationContext = new ValidationContext([
            new Field($command->getCurrentPassword(), 'Current password is invalid.', [
                new MinLength(4),
                new ValidPassword($user, $this->passwordEncoder)
            ]),
            new Field($command->getNewPassword(), 'New password is invalid', [
                new MinLength(4)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}