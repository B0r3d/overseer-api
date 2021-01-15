<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Shared\Domain\Exception\ValidationException;
use Throwable;

class InvalidStacktraceException extends ValidationException
{
    public function __construct()
    {
        $message = 'Invalid stacktrace exception encountered';
        parent::__construct($message);
    }
}