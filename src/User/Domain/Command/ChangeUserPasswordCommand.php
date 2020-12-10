<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class ChangeUserPasswordCommand implements Command
{
    private string $userId;
    private string $currentPassword;
    private string $newPassword;
    private ?string $currentRefreshToken;

    public function __construct(string $userId, string $currentPassword, string $newPassword, ?string $currentRefreshToken = null)
    {
        $this->userId = $userId;
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
        $this->currentRefreshToken = $currentRefreshToken;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getCurrentRefreshToken(): ?string
    {
        return $this->currentRefreshToken;
    }
}