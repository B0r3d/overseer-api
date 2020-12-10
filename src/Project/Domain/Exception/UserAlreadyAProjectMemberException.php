<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\Username;
use Throwable;

final class UserAlreadyAProjectMemberException extends \RuntimeException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withUsername(Username $username)
    {
        $message = 'User "'. $username->value() . '" is already a project member';
        return new self($message);
    }
}