<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

interface UserReadModel
{
    function findOneByUsernameAndEmail(Username $username, Email $email): ?User;
    function findUser(UserId $userId): ?User;
}