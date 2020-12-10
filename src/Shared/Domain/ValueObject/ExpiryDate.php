<?php


namespace Overseer\Shared\Domain\ValueObject;


class ExpiryDate
{
    private \DateTime $value;

    public function __construct(\DateTime $dateTime)
    {
        $this->value = $dateTime;
    }

    public function getValue(): \DateTime
    {
        return $this->value;
    }

    public function isExpired(): bool
    {
        $now = new \DateTime();
        return $now > $this->value;
    }
}