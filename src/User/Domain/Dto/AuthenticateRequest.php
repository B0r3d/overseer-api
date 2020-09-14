<?php


namespace Overseer\User\Domain\Dto;


final class AuthenticateRequest
{
    private string $login;
    private string $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function login(): string
    {
        return $this->login;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function isValid(): bool
    {
        if (!isset($this->login) || !$this->login) {
            return false;
        }

        if (!isset($this->password) || !$this->password) {
            return false;
        }

        return true;
    }
}