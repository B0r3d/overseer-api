<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

final class Subject implements UserInterface, \JsonSerializable
{
    private string $id;
    private string $username;
    private string $password;
    private array $roles;

    private function __construct(string $userId, string $username, string $password, array $roles)
    {
        $this->id = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public static function fromUser(User $user): self
    {
        $roles = array_unique(array_merge($user->getRoles()->toArray(), ['ROLE_USER']));
        return new self(
            $user->getId()->value(),
            $user->getUsername()->getValue(),
            $user->getPassword()->getValue(),
            $roles
        );
    }

    public function getId()
    {
        return $this->id;
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