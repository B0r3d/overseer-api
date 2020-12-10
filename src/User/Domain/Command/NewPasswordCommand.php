<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class NewPasswordCommand implements Command
{
    private string $passwordResetToken;
    private string $newPassword;

    public function __construct(string $passwordResetToken, string $newPassword)
    {
        $this->passwordResetToken = $passwordResetToken;
        $this->newPassword = $newPassword;
    }

    public function getPasswordResetToken(): string
    {
        return $this->passwordResetToken;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}