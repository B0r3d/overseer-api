<?php


namespace Overseer\User\Application\Query;


use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Bus\Query\Result;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\User\Domain\Query\GetUserQuery;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\UserId;

class GetUserQueryHandler implements QueryHandler
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetUserQuery;
    }

    public function __invoke(GetUserQuery $query): Result
    {
        $user = $this->userReadModel->findUser(UserId::fromString($query->getId()));
        return new SingleObjectResult($user);
    }
}