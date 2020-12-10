<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

interface UserReadModel
{
    public function findUser(UserId $userId): ?User;
    public function findOneByLogin(string $login): ?User;
    public function findUserByUsername(Username $username);
    public function findUserByEmail(Email $email);
    public function findUserByRefreshToken(JsonWebToken $token): ?User;
    public function findUserByPasswordResetToken(string $passwordResetTokenId): ?User;
}