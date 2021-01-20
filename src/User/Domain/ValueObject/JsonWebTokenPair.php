<?php


namespace Overseer\User\Domain\ValueObject;


use Overseer\User\Domain\Entity\RefreshToken;

class JsonWebTokenPair implements \JsonSerializable
{
    private JsonWebToken $refreshToken;
    private JsonWebToken $accessToken;

    public function __construct(JsonWebToken $refreshToken, JsonWebToken $accessToken)
    {
        $this->refreshToken = $refreshToken;
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): JsonWebToken
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): JsonWebToken
    {
        return $this->accessToken;
    }

    public function jsonSerialize()
    {
        return [
            'refresh_token' => $this->refreshToken->getToken(),
            'access_token' => $this->accessToken->getToken(),
        ];
    }
}