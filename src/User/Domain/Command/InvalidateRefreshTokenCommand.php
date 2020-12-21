<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class InvalidateRefreshTokenCommand implements Command
{
    private string $refreshToken;

    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}