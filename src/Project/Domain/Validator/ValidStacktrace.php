<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;

class ValidStacktrace implements Specification
{
    public function isSatisfiedBy($value): bool
    {
        foreach ($value as $exception) {
            if (
                empty($exception['exception_class']) ||
                empty($exception['line']) ||
                $exception['line'] <= 0 ||
                empty($exception['file'])
            ) {
                return false;
            }
        }

        return true;
    }
}