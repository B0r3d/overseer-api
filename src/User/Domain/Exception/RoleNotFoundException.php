<?php


namespace Overseer\User\Domain\Exception;


use Throwable;

final class RoleNotFoundException extends \RuntimeException
{
    public function __construct(string $role)
    {
        $message = 'Role "' . $role . '" does not exist in the user\' permissions';
        parent::__construct($message);
    }
}