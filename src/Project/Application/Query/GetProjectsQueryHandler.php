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
        $criteria = [];

        if (!empty($query->getCriteria()['search'])) {
            $criteria['search'] = $query->getCriteria()['search'];
        }

        $sort = [];

        $offset = ($query->getPage() - 1) * 10;

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