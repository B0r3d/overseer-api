<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Username;

class UniqueUsername implements Specification
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $username = new Username($value);
        $user = $this->userReadModel->findUserByUsername($username);

        return $user === null;
    }
}