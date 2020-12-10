<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class UpdateUserSessionCommand implements Command
{
    private string $username;
    private string $oldRefreshToken;
    private string $newRefreshToken;

    public function __construct(string $username, string $oldRefreshToken, string $newRefreshToken)
    {
        $this->username = $username;
        $this->oldRefreshToken = $oldRefreshToken;
        $this->newRefreshToken = $newRefreshToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getOldRefreshToken(): string
    {
        return $this->oldRefreshToken;
    }

    public function getNewRefreshToken(): string
    {
        return $this->newRefreshToken;
    }
}