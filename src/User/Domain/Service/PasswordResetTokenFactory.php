<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\ValueObject\PasswordResetToken;

interface PasswordResetTokenFactory
{
    public function createToken(): PasswordResetToken;
}