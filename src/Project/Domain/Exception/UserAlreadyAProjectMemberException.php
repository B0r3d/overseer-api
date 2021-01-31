<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Exception\ValidationException;
use Throwable;

final class UserAlreadyAProjectMemberException extends ValidationException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withUsername(Username $username)
    {
        $message = 'User "'. $username->getValue() . '" is already a project member';
        return new self($message);
    }
}