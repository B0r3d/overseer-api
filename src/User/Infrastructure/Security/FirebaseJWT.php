<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Entity\RefreshToken;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\JsonWebTokenPair;
use Overseer\User\Domain\ValueObject\RefreshTokenId;

final class FirebaseJWT implements JWT
{
    private string $privateKeyPath;
    private string $publicKeyPath;
    private int $refreshTokenLifetime;
    private int $accessTokenLifetime;
    private string $privateKey;
    private string $publicKey;

    public function __construct(string $privateKeyPath, string $publicKeyPath, int $refreshTokenLifetime, int $accessTokenLifetime)
    {
        $this->privateKeyPath = $privateKeyPath;
        $this->publicKeyPath = $publicKeyPath;
        $this->refreshTokenLifetime = $refreshTokenLifetime;
        $this->accessTokenLifetime = $accessTokenLifetime;
    }

    protected function getPrivateKey()
    {
        if (isset($this->privateKey)) {
            return $this->privateKey;
        }

        if (!file_exists($this->privateKeyPath)) {
            throw new \RuntimeException('Private key file does not exist. File is expected to be located here ' . $this->privateKeyPath);
        }

        return file_get_contents($this->privateKeyPath);
    }

    protected function getPublicKey()
    {
        if (isset($this->publicKey)) {
            return $this->publicKey;
        }

        if (!file_exists($this->publicKeyPath)) {
            throw new \RuntimeException('Public key file does not exist. File is expected to be located here ' . $this->publicKeyPath);
        }

        return file_get_contents($this->publicKeyPath);
    }

    protected function createToken(string $tokenType, string $subject, array $payload, string $expirationTimestamp)
    {
        $now = new \DateTime();
        $jwtPayload = [
            'sub' => $subject,
            'iat' => $now->getTimestamp(),
            'exp' => $expirationTimestamp,
            'payload' => $payload,
            'token_type' => $tokenType,
        ];

        return new JsonWebToken(\Firebase\JWT\JWT::encode(
            $jwtPayload,
            $this->getPrivateKey(),
            'RS256'
        ), $jwtPayload);
    }

    public function verify(JsonWebToken $jwt): bool
    {
        return $jwt->isExpired() === false;
    }

    public function decodeToken(string $jwt): ?JsonWebToken
    {
        try {
            $tokenAsArray = (array) \Firebase\JWT\JWT::decode(
                $jwt,
                $this->getPublicKey(),
                ['RS256']
            );

            return new JsonWebToken($jwt, $tokenAsArray);
        } catch (\Throwable $t) {
            return null;
        }
    }

    public function createAccessToken(User $user): JsonWebToken
    {
        $now = new \DateTime();
        $expiryDate = $now->modify('+' . $this->accessTokenLifetime . 'seconds')->getTimestamp();
        return $this->createToken('access_token', $user->getUsername()->getValue(), [
            'user_id' => $user->getId()->value(),
            'roles' => $user->getRoles()->toArray(),
        ], $expiryDate);
    }

    public function createRefreshToken(User $user): JsonWebToken
    {
        $now = new \DateTime();
        $expiryDate = $now->modify('+' . $this->refreshTokenLifetime . 'seconds')->getTimestamp();
        return $this->createToken(
            'refresh_token',
            $user->getUsername()->getValue(),
            [],
            $expiryDate
        );
    }

    public function createTokens(User $user): JsonWebTokenPair
    {
        return new JsonWebTokenPair(
            $this->createRefreshToken($user),
            $this->createAccessToken($user)
        );
    }
}