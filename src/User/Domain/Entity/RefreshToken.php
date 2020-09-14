<?php


namespace Overseer\User\Domain\Entity;


use Overseer\User\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\ValueObject\RefreshTokenId;

class RefreshToken
{
    private ?int $id;
    private ExpiryDate $expiryDate;
    private RefreshTokenId $uuid;
    private \DateTime $createdAt;
    private bool $valid;
    private User $user;

    public function __construct(User $user, RefreshTokenId $uuid, ExpiryDate $expiryDate)
    {
        $this->user = $user;
        $this->uuid = $uuid;
        $this->expiryDate = $expiryDate;
        $this->valid = true;
        $this->createdAt = new \DateTime();
        $this->id = null;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function expiryDate(): ExpiryDate
    {
        return $this->expiryDate;
    }

    public function uuid(): RefreshTokenId
    {
        return $this->uuid;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function isExpired(): bool
    {
        return $this->expiryDate->isExpired();
    }
}