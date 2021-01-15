<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class GreaterThan implements Specification
{
    private int $number;

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function isSatisfiedBy($value): bool
    {
        return $value > $this->number;
    }
}