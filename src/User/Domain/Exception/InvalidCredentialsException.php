<?php


namespace Overseer\User\Domain\Exception;


use Overseer\Shared\Domain\Exception\ValidationException;

final class InvalidCredentialsException extends ValidationException
{
    public function __construct()
    {
        $message = 'Invalid username or password.';
        parent::__construct($message);
    }
}