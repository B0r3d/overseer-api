<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Exception\InvalidCredentialsException;
use Overseer\User\Domain\Service\Authenticator;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\AuthUser;
use Overseer\User\Domain\ValueObject\PlainPassword;

final class LoginPasswordAuthenticator implements Authenticator
{
    private UserReadModel $userReadModel;
    private UserPasswordEncoder $userPasswordEncoder;

    public function __construct(UserReadModel $userReadModel, UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userReadModel = $userReadModel;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function authenticate(AuthUser $authUser): User
    {
        $user = $this->userReadModel->findOneByLogin($authUser->getLogin());
        $passwordToCheck = new PlainPassword($authUser->getPassword());

        if (!$user || !$this->userPasswordEncoder->isValidPassword($user->getPassword(), $passwordToCheck)) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}