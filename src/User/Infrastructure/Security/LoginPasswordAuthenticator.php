<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Dto\AuthenticateRequest;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Exception\InvalidCredentialsException;
use Overseer\User\Domain\Exception\UserNotFoundException;
use Overseer\User\Domain\Service\Authenticator;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Password;

final class LoginPasswordAuthenticator implements Authenticator
{
    private UserReadModel $userReadModel;
    private UserPasswordEncoder $userPasswordEncoder;

    public function __construct(UserReadModel $userReadModel, UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userReadModel = $userReadModel;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    function authenticate(AuthenticateRequest $authenticateRequest): User
    {
        $user = $this->userReadModel->findOneByLogin($authenticateRequest->login());

        if (!$user) {
            throw new UserNotFoundException();
        }

        $passwordToCheck = new Password($authenticateRequest->password());

        if (!$this->userPasswordEncoder->isValidPassword($user->password(), $passwordToCheck)) {
            throw new InvalidCredentialsException($user->username());
        }

        return $user;
    }
}