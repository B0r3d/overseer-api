<?php


namespace Overseer\User\Domain\Validator;


use Overseer\Shared\Domain\Validator\Specification;
use Overseer\User\Domain\Service\UserReadModel;

class ValidUser implements Specification
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }


    public function isSatisfiedBy($value): bool
    {
        $user = $this->userReadModel->findOneByLogin($value);
        return $user !== null;
    }
}