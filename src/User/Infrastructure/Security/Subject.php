<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

final class Subject implements UserInterface, \JsonSerializable
{
    private string $username;
    private string $password;
    private array $roles;

    private function __construct(string $username, string $password, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->username()->value(),
            $user->password()->value(),
            array_merge($user->roles()->toArray(), ['ROLE_USER']),
        );
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function jsonSerialize()
    {
        return [
            'username' => $this->username,
            'roles' => $this->roles,
        ];
    }
}