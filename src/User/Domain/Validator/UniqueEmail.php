<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Email;

class UniqueEmail implements Specification
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $email = new Email($value);
        $user = $this->userReadModel->findUserByEmail($email);

        return $user === null;
    }
}