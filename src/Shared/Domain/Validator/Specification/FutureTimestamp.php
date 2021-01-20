<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class FutureTimestamp implements Specification
{
    public function isSatisfiedBy($value): bool
    {
        $now = new \DateTime();
        return $value > $now;
    }
}