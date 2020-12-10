<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\PlainPassword;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

interface UserFactory
{
    public function createUser(UserId $userId, Username $username, Email $email, PlainPassword $password): User;
}