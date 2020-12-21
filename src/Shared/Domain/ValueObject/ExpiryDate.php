<?php


namespace Overseer\Shared\Domain\ValueObject;


class ExpiryDate
{
    private ?\DateTime $value;

    public function __construct(\DateTime $dateTime)
    {
        $this->value = $dateTime;
    }

    public function getValue(): ?\DateTime
    {
        if (!isset($this->value)) {
            $this->value = null;
        }

        return $this->value;
    }

    public function isExpired(): bool
    {
        if (!$this->getValue()) {
            return false;
        }

        $now = new \DateTime();
        return $now > $this->value;
    }
}