<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\RefreshToken;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\JsonWebTokenPair;

interface JWT
{
    const REFRESH_TOKEN_COOKIE = 'refresh_token';

    public function createAccessToken(User $user): JsonWebToken;
    public function createRefreshToken(User $user): JsonWebToken;
    public function verify(JsonWebToken $jwt): bool;
    public function decodeToken(string $jwt): ?JsonWebToken;
    public function createTokens(User $user): JsonWebTokenPair;
}