<?php


namespace Overseer\User\Domain\ValueObject;


use Overseer\User\Domain\Enum\UserRole;
use Overseer\User\Domain\Exception\RoleAlreadyAssignedException;
use Overseer\User\Domain\Exception\RoleNotFoundException;

final class Roles
{
    private array $array;

    public function __construct()
    {
        $this->array[] = UserRole::USER()->getValue();
    }

    public function addRole(UserRole $role): void
    {
        if (in_array($role->getValue(), $this->array)) {
            throw new RoleAlreadyAssignedException($role);
        }

        $this->array[] = $role->getValue();
    }

    public function removeRole(UserRole $role): void
    {
        $index = array_search($role->getValue(), $this->array);

        if ($index === false) {
            throw new RoleNotFoundException($role);
        }

        unset($this->array[$index]);
        $this->array = array_values($this->array); // Reindex the array
    }

    public function toArray(): array
    {
        return array_unique($this->array);
    }
}