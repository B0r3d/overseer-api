<?php


namespace Overseer\User\Domain\Dto;


use Overseer\User\Domain\Entity\User;

class UserResource implements \JsonSerializable
{
    private string $id;
    private string $username;
    private string $email;
    private array $roles;
    private string $createdAt;

    public function __construct(User $user)
    {
        $this->id = $user->getId()->value();
        $this->username = $user->getUsername()->getValue();
        $this->email = $user->getEmail()->getValue();
        $this->roles = $user->getRoles()->toArray();
        $this->createdAt = $user->getCreatedAt()->format(\DateTime::ISO8601);
    }


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
            'created_at' => $this->createdAt,
        ];
    }
}