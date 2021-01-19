<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationsQuery;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Domain\ValueObject\Uuid;

class GetTelegramIntegrationsQueryHandler implements QueryHandler
{
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private ProjectMembershipChecker $projectMembershipChecker;

    public function __construct(TelegramIntegrationReadModel $telegramIntegrationReadModel, ProjectMembershipChecker $projectMembershipChecker)
    {
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->projectMembershipChecker = $projectMembershipChecker;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetTelegramIntegrationsQuery;
    }

    public function __invoke(GetTelegramIntegrationsQuery $query)
    {
        if (!\Ramsey\Uuid\Uuid::isValid($query->getProjectId())) {
            return new NotFoundException();
        }

        $projectId = Uuid::fromString($query->getProjectId());

        if (!$this->projectMembershipChecker->isMember($projectId, $query->getIssuedBy())) {
            throw new UnauthorizedException();
        }

        $limit = 10;
        $offset = ($query->getPage() - 1) * $limit;

        $integrations = $this->telegramIntegrationReadModel->findIntegrations(
            Uuid::fromString($query->getProjectId()),
            $query->getCriteria(),
            $query->getSort(),
            $limit,
            $offset
        );

        $count = $this->telegramIntegrationReadModel->findIntegrationsCount(
            Uuid::fromString($query->getProjectId()),
            $query->getCriteria()
        );

        return new PaginatedQueryResult(
            $integrations,
            $count
        );
    }
}