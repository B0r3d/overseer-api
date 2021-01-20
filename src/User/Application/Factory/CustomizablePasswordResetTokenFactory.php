<?php


namespace Overseer\User\Application\Factory;


use Overseer\Shared\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\Service\PasswordResetTokenFactory;
use Overseer\User\Domain\ValueObject\PasswordResetToken;
use Overseer\User\Domain\ValueObject\PasswordResetTokenId;

class CustomizablePasswordResetTokenFactory implements PasswordResetTokenFactory
{
    private int $passwordResetTokenLifetime;

    public function __construct(int $passwordResetTokenLifetime)
    {
        $this->passwordResetTokenLifetime = $passwordResetTokenLifetime;
    }

    public function createToken(): PasswordResetToken
    {
        $now = new \DateTime();
        $now->modify('+' . $this->passwordResetTokenLifetime . 'seconds');

        $expiryDate = new ExpiryDate($now);
        $passwordResetTokenId = PasswordResetTokenId::random();

        return new PasswordResetToken($passwordResetTokenId, $expiryDate);
    }
}