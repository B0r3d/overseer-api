<?php


namespace Overseer\User\Application\Command\RegisterUser;


use Overseer\Shared\Domain\Bus\Command\Command;

class RegisterUser implements Command
{
    private string $username;
    private string $uuid;
    private string $email;
    private string $password;

    function __construct(string $username, string $uuid, string $email, string $password)
    {
        $this->username = $username;
        $this->uuid = $uuid;
        $this->email = $email;
        $this->password = $password;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}