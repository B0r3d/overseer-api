<?php


namespace Overseer\Project\Domain\ValueObject;


use Overseer\Shared\Domain\ValueObject\StringValueObject;

final class Slug extends StringValueObject
{
    public function __construct(string $value)
    {
        if (!preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $value)) {
            throw new \InvalidArgumentException('"'. $value . '" is not a valid slug.');
        }

        parent::__construct($value);
    }
}