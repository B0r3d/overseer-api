<?php


namespace Overseer\Project\Application\Query;


use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Query\GetProjectErrorsQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\Validator\Specification\ValidTimestamp;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Ramsey\Uuid\Uuid;

class GetProjectErrorsQueryHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetProjectErrorsQuery;
    }

    public function __invoke(GetProjectErrorsQuery $query)
    {
        if (!Uuid::isValid($query->getProjectId())) {
            throw new ProjectNotFoundException();
        }

        $project = $this->projectReadModel->findById(ProjectId::fromString($query->getProjectId()));

        if (!$project) {
            throw new ProjectNotFoundException();
        }

        $members = $project->getMembers();
        $username = new Username($query->getIssuedBy());

        if (!$members->findMemberWithUsername($username)) {
            throw new UnauthorizedException();
        }

        $criteria = [];
        $timestampValidator = new ValidTimestamp();

        if (!empty($query->getCriteria()['search'])) {
            $criteria['search'] = $query->getCriteria()['search'];
        }

        if (!empty($query->getCriteria()['date_from']) && $timestampValidator->isSatisfiedBy($query->getCriteria()['date_from'])) {
            $criteria['date_from'] = new \DateTime('@' . $query->getCriteria()['date_from']);
        }

        if (!empty($query->getCriteria()['date_to']) && $timestampValidator->isSatisfiedBy($query->getCriteria()['date_to'])) {
            $criteria['date_to'] = new \DateTime('@' . $query->getCriteria()['date_to']);
        }

        $sort = [];

        if (array_search('desc(occurred_at)', $query->getSort()) !== false) {
            $sort['occurred_at'] = 'DESC';
        }

        if (array_search('asc(occurred_at)', $query->getSort()) !== false) {
            $sort['occurred_at'] = 'ASC';
        }

        $offset = ($query->getPage() - 1) * 10;
        $errors = $this->projectReadModel->getProjectErrors(
            $project,
            $criteria,
            $sort,
            10,
            $offset
        );

        $errorsCount = $this->projectReadModel->countProjectErrors(
            $project,
            $criteria,
        );

        return new PaginatedQueryResult(
            $errors,
            $errorsCount,
            $query->getPage(),
        );
    }
}