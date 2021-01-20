<?php


namespace Overseer\User\Domain\ValueObject;


use Overseer\Shared\Domain\ValueObject\StringValueObject;

final class Email extends StringValueObject
{
    public function __construct(string $value)
    {
        if (strlen($value) === 0 || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address provided. Got "' . $value . '".');
        }

        parent::__construct(strtolower($value));
    }
}