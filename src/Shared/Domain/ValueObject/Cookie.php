<?php


namespace Overseer\Shared\Domain\ValueObject;


class Cookie
{
    private string $name;
    private string $value;
    private string $expiryTimestamp;
    private bool $httpOnly;

    public function __construct(string $name, string $value, string $expiryTimestamp, bool $httpOnly = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expiryTimestamp = $expiryTimestamp;
        $this->httpOnly = $httpOnly;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpiryTimestamp(): string
    {
        return $this->expiryTimestamp;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }
}