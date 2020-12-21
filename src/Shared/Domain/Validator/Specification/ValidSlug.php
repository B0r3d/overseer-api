<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class ValidSlug implements Specification
{
    private const SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public function isSatisfiedBy($value): bool
    {
        return preg_match(self::SLUG_PATTERN, $value);
    }
}