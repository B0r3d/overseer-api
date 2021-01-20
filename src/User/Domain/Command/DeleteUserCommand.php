<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class DeleteUserCommand implements Command
{
    private string $userId;
    private string $currentPassword;

    public function __construct(string $userId, string $currentPassword)
    {
        $this->userId = $userId;
        $this->currentPassword = $currentPassword;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }
}