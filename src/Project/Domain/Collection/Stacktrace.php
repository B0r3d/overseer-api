<?php


namespace Overseer\Project\Domain\Collection;


use Overseer\Project\Domain\ValueObject\Exception;

class Stacktrace extends \ArrayObject
{
    public function addException(Exception $exception): void
    {
        $this[] = $exception;
    }
}