<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserReadModel;

class RefreshTokenNotTerminated implements Specification
{
    private UserReadModel $userReadModel;
    private JWT $jwt;

    public function __construct(UserReadModel $userReadModel, JWT $jwt)
    {
        $this->userReadModel = $userReadModel;
        $this->jwt = $jwt;
    }

    public function isSatisfiedBy($value): bool
    {
        $token = $this->jwt->decodeToken($value);

        if (!$token) {
            return false;
        }

        $user = $this->userReadModel->findUserByRefreshToken($token);

        if (!$user) {
            return false;
        }

        $sessions = $user->getSessions();
        $session = $sessions->findByRefreshToken($token);

        if (!$session) {
            return false;
        }

        return $session->isValid();
    }
}