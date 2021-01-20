<?php


namespace Overseer\User\Application\Command\NewPasswordCommand;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\MinLength;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Overseer\User\Domain\Command\NewPasswordCommand;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Validator\ValidPasswordResetToken;

final class NewPasswordCommandValidator implements CommandValidator
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof NewPasswordCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . NewPasswordCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getPasswordResetToken(), 'Invalid or expired reset token', [
                new ValidPasswordResetToken($this->userReadModel),
            ]),
            new Field($command->getNewPassword(), 'Invalid password', [
                new MinLength(4)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}