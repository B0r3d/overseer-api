<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class StartUserSessionCommand implements Command
{
    private string $refreshToken;
    private string $sessionId;
    private string $userId;
    private string $refreshTokenExpiryTimestamp;
    private string $refreshTokenIssuedAtTimestamp;

    public function __construct(string $refreshToken, string $sessionId, string $userId, string $refreshTokenExpiryTimestamp, string $refreshTokenIssuedAtTimestamp)
    {
        $this->refreshToken = $refreshToken;
        $this->sessionId = $sessionId;
        $this->userId = $userId;
        $this->refreshTokenExpiryTimestamp = $refreshTokenExpiryTimestamp;
        $this->refreshTokenIssuedAtTimestamp = $refreshTokenIssuedAtTimestamp;
    }


    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getRefreshTokenExpiryTimestamp(): string
    {
        return $this->refreshTokenExpiryTimestamp;
    }

    public function getRefreshTokenIssuedAtTimestamp(): string
    {
        return $this->refreshTokenIssuedAtTimestamp;
    }
}