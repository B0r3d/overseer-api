<?php


namespace Overseer\User\Domain\Exception;


final class InvalidRoleException extends \RuntimeException
{
    public function __construct(string $role, array $allowedRoles)
    {
        $message = 'Invalid role "' . $role . '", allowed roles are ' . join(', ', $allowedRoles);
        parent::__construct($message);
    }
}