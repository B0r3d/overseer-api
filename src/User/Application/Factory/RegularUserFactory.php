<?php


namespace Overseer\User\Application\Factory;


use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserFactory;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\PlainPassword;
use Overseer\User\Domain\ValueObject\Roles;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

class RegularUserFactory implements UserFactory
{
    private UserPasswordEncoder $userPasswordEncoder;

    public function __construct(UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function createUser(UserId $userId, Username $username, Email $email, PlainPassword $password): User
    {
        $roles = new Roles();
        $hashedPassword = $this->userPasswordEncoder->encodePassword($password);

        return new User(
            $userId,
            $username,
            $email,
            $hashedPassword,
            $roles
        );
    }
}