<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class Url implements Specification
{
    public function isSatisfiedBy($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }
}