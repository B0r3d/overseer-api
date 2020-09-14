<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Entity\RefreshToken;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\RefreshTokenId;

final class FirebaseJWT implements JWT
{
    private string $privateKey;
    private string $publicKey;
    private int $refreshTokenLifetime;
    private int $accessTokenLifetime;

    public function __construct(string $privateKeyPath, string $publicKeyPath, int $refreshTokenLifetime, int $accessTokenLifetime)
    {
        $this->privateKey = file_get_contents($privateKeyPath);
        $this->publicKey = file_get_contents($publicKeyPath);
        $this->refreshTokenLifetime = $refreshTokenLifetime;
        $this->accessTokenLifetime = $accessTokenLifetime;
    }

    public function issueToken(User $user): JsonWebToken
    {
        $now = new \DateTime();
        $payload = [
            'sub' => $user->username()->value(),
            'exp' => (clone $now)->modify('+' . $this->accessTokenLifetime . 'seconds')->getTimestamp(),
            'iat' => $now->getTimestamp(),
            'payload' => $user->jwtPayload(),
        ];
        return new JsonWebToken(\Firebase\JWT\JWT::encode($payload, $this->privateKey, 'RS256'));
    }

    public function verify(JsonWebToken $jwt): bool
    {
        try {
            $jwt = $this->decodeToken($jwt->token());
            $now = new \DateTime();
            return $now->getTimestamp() < $jwt['exp'];
        } catch(\Throwable $t) {
            return false;
        }
    }

    public function createRefreshToken(User $user): RefreshToken
    {
        $refreshTokenId = RefreshTokenId::random();
        $expiryDate = new ExpiryDate((new \DateTime())->modify('+' . $this->refreshTokenLifetime . 'seconds'));
        return new RefreshToken($user, $refreshTokenId, $expiryDate);
    }

    public function decodeToken(string $jwt): array
    {
        return (array) \Firebase\JWT\JWT::decode($jwt, $this->publicKey, ['RS256']);
    }
}