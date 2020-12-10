<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Dto\AuthenticateRequest;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\ValueObject\AuthUser;

interface Authenticator
{
    public function authenticate(AuthUser $authUser): User;
}