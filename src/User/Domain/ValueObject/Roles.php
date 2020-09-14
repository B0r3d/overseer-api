<?php


namespace Overseer\User\Domain\ValueObject;


use Overseer\User\Domain\Exception\InvalidRoleException;
use Overseer\User\Domain\Exception\RoleAlreadyAssignedException;
use Overseer\User\Domain\Exception\RoleNotFoundException;

final class Roles
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    private array $array;

    public function __construct()
    {
        $this->array[] = self::ROLE_USER;
    }

    private function listAllowedRoles(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
            self::ROLE_SUPER_ADMIN,
        ];
    }
    public function addRole(string $role): void
    {
        if (!in_array($role, $this->listAllowedRoles())) {
            throw new InvalidRoleException($role, $this->listAllowedRoles());
        }

        if (in_array($role, $this->array)) {
            throw new RoleAlreadyAssignedException($role);
        }

        $this->array[] = $role;
    }

    public function removeRole(string $role): void
    {
        $index = array_search($role, $this->array);

        if ($index === false) {
            throw new RoleNotFoundException($role);
        }

        unset($this->array[$index]);
        $this->array = array_values($this->array); // Reindex the array
    }

    public function toArray(): array
    {
        return $this->array;
    }
}