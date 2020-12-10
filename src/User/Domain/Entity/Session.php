<?php


namespace Overseer\User\Domain\Entity;


use Overseer\Shared\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\Enum\SessionStatus;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\SessionId;
use Overseer\User\Domain\ValueObject\UserId;

class Session
{
    private User $user;
    private SessionId $_id;
    private string $id;
    private ExpiryDate $expiryDate;
    private \DateTime $sessionStart;
    private string $refreshToken;
    private SessionStatus $status;
    private ?\DateTime $lastRefreshDate;

    public function __construct(User $user, SessionId $sessionId, ExpiryDate $expiryDate, \DateTime $sessionStart, string $refreshToken)
    {
        $this->user = $user;
        $this->id = (string)$sessionId;
        $this->_id = $sessionId;
        $this->expiryDate = $expiryDate;
        $this->sessionStart = $sessionStart;
        $this->refreshToken = $refreshToken;
        $this->status = SessionStatus::VALID();
        $this->lastRefreshDate = null;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): SessionId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        return SessionId::fromString($this->id);
    }

    public function getExpiryDate(): ExpiryDate
    {
        return $this->expiryDate;
    }

    public function getSessionStart(): \DateTime
    {
        return $this->sessionStart;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function getLastRefreshDate(): ?\DateTime
    {
        return $this->lastRefreshDate;
    }

    public function isValid(): bool
    {
        if ($this->expiryDate->isExpired()) {
            return false;
        }

        if (!$this->status->equals(SessionStatus::VALID())) {
            return false;
        }

        return true;
    }

    public function refresh(JsonWebToken $token)
    {
        $now = new \DateTime();
        $expiryDate = new ExpiryDate((clone $now)->setTimestamp($token->getExpiryTime()));

        $this->expiryDate = $expiryDate;
        $this->refreshToken = $token->getToken();
        $this->lastRefreshDate = $now;
    }

    public function invalidate()
    {
        $this->status = SessionStatus::TERMINATED();
    }
}