<?php


namespace Overseer\Project\Application\Query;


use Overseer\Project\Domain\Query\GetProjectsQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;

class GetProjectsQueryHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetProjectsQuery;
    }

    public function __invoke(GetProjectsQuery $query)
    {
        // TODO bind criteria and sort based on query
        $criteria = [];
        $sort = [];

        $offset = (1 - $query->getPage()) * 10;

        $dbResult = $this->projectReadModel->getProjects(
            $query->getIssuedBy(),
            $criteria,
            $sort,
            10,
            $offset
        );

        $totalCount = $this->projectReadModel->count(
            $query->getIssuedBy(),
            $criteria
        );

        return new PaginatedQueryResult(
            $dbResult,
            $totalCount,
            $query->getPage()
        );
    }
}