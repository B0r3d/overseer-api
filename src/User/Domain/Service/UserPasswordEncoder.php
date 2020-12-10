<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\ValueObject\HashedPassword;
use Overseer\User\Domain\ValueObject\Password;
use Overseer\User\Domain\ValueObject\PlainPassword;

interface UserPasswordEncoder
{
    function encodePassword(PlainPassword $password): HashedPassword;
    function isValidPassword(HashedPassword $userPassword, PlainPassword $passwordToCheck): bool;
}