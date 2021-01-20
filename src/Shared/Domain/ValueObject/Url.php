<?php


namespace Overseer\Shared\Domain\ValueObject;


class Url extends StringValueObject
{
    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid url');
        }

        parent::__construct($value);
    }
}