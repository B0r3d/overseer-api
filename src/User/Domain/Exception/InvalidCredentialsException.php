<?php


namespace Overseer\User\Domain\Exception;


use Overseer\User\Domain\ValueObject\Username;

final class InvalidCredentialsException extends \RuntimeException
{
    private Username $username;

    public function __construct(string $username)
    {
        $this->username = new Username($username);
        $message = 'Failed to login user with username "' . $username . '".';
        parent::__construct($message);
    }

    public function username(): Username
    {
        return $this->username;
    }
}