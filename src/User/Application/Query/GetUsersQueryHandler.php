<?php


namespace Overseer\User\Application\Query;


use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Bus\Query\Result;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\User\Domain\Query\GetUsersQuery;
use Overseer\User\Domain\Service\UserReadModel;

class GetUsersQueryHandler implements QueryHandler
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetUsersQuery;
    }

    public function __invoke(GetUsersQuery $query): Result
    {
        $dbResult = $this->userReadModel->getUsers(
            $query->getCriteria(),
            [],
            10,
            (1 - $query->getPage()) * 10
        );

        $count = $this->userReadModel->count($query->getCriteria());

        return new PaginatedQueryResult(
            $dbResult,
            $count,
            $query->getPage()
        );
    }
}