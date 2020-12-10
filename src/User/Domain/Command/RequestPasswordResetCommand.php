<?php


namespace Overseer\User\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class RequestPasswordResetCommand implements Command
{
    private string $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function getLogin(): string
    {
        return $this->login;
    }
}