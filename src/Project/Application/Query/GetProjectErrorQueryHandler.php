<?php


namespace Overseer\Project\Application\Query;


use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Query\GetProjectErrorQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Ramsey\Uuid\Uuid;

class GetProjectErrorQueryHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetProjectErrorQuery;
    }

    public function __invoke(GetProjectErrorQuery $query)
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

        if (!Uuid::isValid($query->getId())) {
            throw new NotFoundException('Error not found');
        }

        $error = $project->getErrors()->findById(ErrorId::fromString($query->getId()));

        if (!$error) {
            throw new NotFoundException('Error not found');
        }

        return new SingleObjectResult($error);
    }
}