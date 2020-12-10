<?php


namespace Overseer\Shared\Domain\Validator;


interface Specification
{
    public function isSatisfiedBy($value): bool;
}