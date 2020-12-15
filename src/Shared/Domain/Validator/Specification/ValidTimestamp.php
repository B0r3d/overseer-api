<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class ValidTimestamp implements Specification
{

    public function isSatisfiedBy($value): bool
    {
        try {
            new \DateTime('@' . $value);
            return true;
        } catch(\Throwable $t) {
            return false;
        }
    }
}