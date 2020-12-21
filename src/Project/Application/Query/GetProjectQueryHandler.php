<?php


namespace Overseer\Project\Application\Query;


use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Query\GetProjectQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GetProjectQueryHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetProjectQuery;
    }

    public function __invoke(GetProjectQuery $query)
    {
        if (!Uuid::isValid($query->getId())) {
            throw new ProjectNotFoundException();
        }

        $project = $this->projectReadModel->findById(ProjectId::fromString($query->getId()));

        if (!$project) {
            throw new ProjectNotFoundException();
        }

        $members = $project->getMembers();

        $member = $members->findMemberWithUsername(new Username($query->getIssuedBy()));
        if ($member) {
            return new SingleObjectResult($project);
        }

        $invitations = $project->getInvitations();

        $invitation = $invitations->findInvitationWithUsername(new Username($query->getIssuedBy()));
        if ($invitation && $invitation->getStatus()->equals(InvitationStatus::INVITED())) {
            return new SingleObjectResult($project);
        }

        throw new UnauthorizedHttpException('Forbidden resource');
    }
}