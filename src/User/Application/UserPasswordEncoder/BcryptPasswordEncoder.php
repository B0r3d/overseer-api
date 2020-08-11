<?php


namespace Overseer\User\Application\UserPasswordEncoder;


use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\Password;

class BcryptPasswordEncoder implements UserPasswordEncoder
{
    private string $pepper;
    private int $cost;

    public function __construct(string $pepper, int $cost)
    {
        $this->pepper = $pepper;
        $this->cost = $cost;
    }

    function encodePassword(string $password): Password
    {
        $seasonedPassword = $this->pepper . $password;

        $hash = password_hash($seasonedPassword, PASSWORD_BCRYPT, ['cost' => $this->cost]);
        return new Password($hash);
    }

    function isValidPassword(Password $userPassword, Password $passwordToCheck): bool
    {
        dump("Implement me");
        die;
    }
}