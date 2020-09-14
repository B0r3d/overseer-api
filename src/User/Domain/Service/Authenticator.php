<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Dto\AuthenticateRequest;
use Overseer\User\Domain\Entity\User;

interface Authenticator
{
    function authenticate(AuthenticateRequest $authenticateRequest): User;
}