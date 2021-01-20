<?php


namespace Overseer\User\Domain\Dto;


use Overseer\User\Domain\Entity\User;

class InviteUserResource implements \JsonSerializable
{
    private string $id;
    private string $username;
    private string $email;

    public function __construct(User $user)
    {
        $this->id = $user->getId()->value();
        $this->username = $user->getUsername()->getValue();
        $this->email = $user->getEmail()->getValue();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
    }
}