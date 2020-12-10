<?php


namespace Overseer\User\Application\UserPasswordEncoder;


use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\HashedPassword;
use Overseer\User\Domain\ValueObject\PlainPassword;

final class BcryptPasswordEncoder implements UserPasswordEncoder
{
    private string $pepper;
    private int $cost;

    public function __construct(string $pepper, int $cost)
    {
        $this->pepper = $pepper;
        $this->cost = $cost;
    }

    function encodePassword(PlainPassword $password): HashedPassword
    {
        $seasonedPassword = $this->pepper . $password->getValue();

        $hash = password_hash($seasonedPassword, PASSWORD_BCRYPT, ['cost' => $this->cost]);
        return new HashedPassword($hash);
    }

    function isValidPassword(HashedPassword $userPassword, PlainPassword $passwordToCheck): bool
    {
        $seasonedPassword = $this->pepper . $passwordToCheck->getValue();
        return password_verify(
            $seasonedPassword,
            $userPassword->getValue()
        );
    }
}