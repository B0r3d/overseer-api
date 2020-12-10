<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class MinLength implements Specification
{
    private int $minLength;

    public function __construct(int $minLength)
    {
        if ($minLength <= 0) {
            throw new \InvalidArgumentException('Length must be a positive number');
        }
        $this->minLength = $minLength;
    }


    public function isSatisfiedBy($value): bool
    {
        return mb_strlen($value) >= $this->minLength;
    }
}