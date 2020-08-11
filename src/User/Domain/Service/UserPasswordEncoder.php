<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\ValueObject\Password;

interface UserPasswordEncoder
{
    function encodePassword(string $password): Password;
    function isValidPassword(Password $userPassword, Password $passwordToCheck): bool;
}