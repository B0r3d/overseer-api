<?php


namespace Overseer\Shared\Domain\ValueObject;


class Email extends StringValueObject
{
    public function __construct(string $value)
    {
        if (mb_strlen($value) === 0 || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address. Got: ' . $value);
        }

        parent::__construct(strtolower($value));
    }
}