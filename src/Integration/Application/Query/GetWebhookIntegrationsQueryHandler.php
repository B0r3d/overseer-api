<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationsQuery;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Domain\ValueObject\Uuid;

class GetWebhookIntegrationsQueryHandler implements QueryHandler
{
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;
    private ProjectMembershipChecker $projectMembershipChecker;

    public function __construct(WebhookIntegrationReadModel $webhookIntegrationReadModel, ProjectMembershipChecker $projectMembershipChecker)
    {
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
        $this->projectMembershipChecker = $projectMembershipChecker;
    }

    public function handles(Query $query): bool
    {
        return $query instanceof GetWebhookIntegrationsQuery;
    }

    public function __invoke(GetWebhookIntegrationsQuery $query)
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

        $integrations = $this->webhookIntegrationReadModel->findIntegrations(
            Uuid::fromString($query->getProjectId()),
            $query->getCriteria(),
            $query->getSort(),
            $limit,
            $offset
        );

        $count = $this->webhookIntegrationReadModel->findIntegrationsCount(
            Uuid::fromString($query->getProjectId()),
            $query->getCriteria()
        );

        return new PaginatedQueryResult(
            $integrations,
            $count
        );
    }
}