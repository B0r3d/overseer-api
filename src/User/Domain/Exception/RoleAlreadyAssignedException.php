<?php


namespace Overseer\User\Domain\Exception;


final class RoleAlreadyAssignedException extends \RuntimeException
{
    public function __construct(string $role)
    {
        $message = 'Role "' . $role . '" was already assigned';
        parent::__construct($message);
    }
}