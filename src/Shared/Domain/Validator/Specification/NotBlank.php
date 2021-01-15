<?php


namespace Overseer\Shared\Domain\Validator\Specification;


use Overseer\Shared\Domain\Validator\Specification;

class NotBlank implements Specification
{

    public function isSatisfiedBy($value): bool
    {
        return mb_strlen($value) !== 0;
    }
}