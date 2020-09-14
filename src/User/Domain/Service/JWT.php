<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\RefreshToken;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\JsonWebToken;

interface JWT
{
    public function issueToken(User $user): JsonWebToken;
    public function createRefreshToken(User $user): RefreshToken;
    public function verify(JsonWebToken $jwt): bool;
    public function decodeToken(string $jwt): array;
}