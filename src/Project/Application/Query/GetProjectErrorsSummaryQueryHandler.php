<?php


namespace Overseer\Project\Application\Query;


use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Query\GetProjectErrorsSummaryQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Validator\Specification\ValidTimestamp;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Ramsey\Uuid\Uuid;

class GetProjectErrorsSummaryQueryHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetProjectErrorsSummaryQuery;
    }

    public function __invoke(GetProjectErrorsSummaryQuery $query)
    {
        if (!Uuid::isValid($query->getId())) {
            throw new ProjectNotFoundException();
        }

        $project = $this->projectReadModel->findById(ProjectId::fromString($query->getId()));

        if (!$project) {
            throw new ProjectNotFoundException();
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

        $summary = $this->projectReadModel->getErrorsSummary(
            $project,
            $criteria,
        );

        return new SingleObjectResult($summary);
    }
}