<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;
use Ramsey\Uuid\Uuid;

class ValidUuid implements Specification
{
    public function isSatisfiedBy($value): bool
    {
        return Uuid::isValid($value);
    }
}