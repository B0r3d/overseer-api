<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\PlainPassword;

class ValidPassword implements Specification
{
    private User $user;
    private UserPasswordEncoder $encoder;

    public function __construct(User $user, UserPasswordEncoder $encoder)
    {
        $this->user = $user;
        $this->encoder = $encoder;
    }

    public function isSatisfiedBy($value): bool
    {
        return $this->encoder->isValidPassword(
            $this->user->getPassword(),
            new PlainPassword($value)
        );
    }
}