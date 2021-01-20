<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;

class ValidUsername implements Specification
{
    private const PATTERN = '/^(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/';

    public function isSatisfiedBy($value): bool
    {
        return preg_match(self::PATTERN, $value);
    }
}