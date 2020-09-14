<?php


namespace Overseer\User\Domain\Dto;


use Overseer\Shared\Domain\ValueObject\Uuid;

final class RegisterUserRequest
{
    private string $username;
    private string $email;
    private string $password;
    private string $uuid;

    private function __construct()
    {
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function uuid(): string
    {
        if (!isset($this->uuid)) {
            $this->uuid = Uuid::random()->value();
        }
        return $this->uuid;
    }

    public function isValid(): bool
    {
        if (!isset($this->username) || !$this->username) {
            return false;
        }

        if (!isset($this->email) || !$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!isset($this->password)|| !$this->password) {
            return false;
        }

        return true;
    }
}