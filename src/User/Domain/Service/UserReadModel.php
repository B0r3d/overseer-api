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
    public function findUserByUsername(Username $username): ?User;
    public function findUserByEmail(Email $email): ?User;
    public function findUserByRefreshToken(JsonWebToken $token): ?User;
    public function findUserByPasswordResetToken(string $passwordResetTokenId): ?User;
    public function getUsers(array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function count(array $criteria = []);
}