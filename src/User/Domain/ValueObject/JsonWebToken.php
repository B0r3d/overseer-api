<?php


namespace Overseer\User\Domain\ValueObject;


final class JsonWebToken
{
    private string $token;
    private array $payload;

    public function __construct(string $token, array $payload)
    {
        $this->token = $token;
        $this->payload = $payload;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiryTime(): string
    {
        return $this->payload['exp'];
    }

    public function getIssuedAt(): string
    {
        return $this->payload['iat'];
    }

    public function isExpired()
    {
        $now = new \DateTime();
        return $this->getExpiryTime() < $now->getTimestamp();
    }

    public function getSubject()
    {
        return $this->payload['sub'];
    }
}