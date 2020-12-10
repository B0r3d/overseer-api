<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Service\JWT;

class ValidRefreshToken implements Specification
{
    private JWT $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    public function isSatisfiedBy($value): bool
    {
        $token = $this->jwt->decodeToken($value);

        if (!$token) {
            return false;
        }

        return $token->isExpired() !== true;
    }
}