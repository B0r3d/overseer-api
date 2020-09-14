<?php


namespace Overseer\User\Domain\ValueObject;


final class ExpiryDate
{
    private \DateTime $value;

    public function __construct(\DateTime $value)
    {
        $now = new \DateTime();

        if ($now > $value) {
            throw new \InvalidArgumentException('The expiry date must be in the future');
        }

        $this->value = $value;
    }

    public function value(): \DateTime
    {
        return $this->value;
    }

    public function isExpired(): bool
    {
        $now = new \DateTime();
        return $now >= $this->value;
    }
}