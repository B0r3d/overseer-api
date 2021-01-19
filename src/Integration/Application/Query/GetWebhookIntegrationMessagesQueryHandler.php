<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationMessagesQuery;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Ramsey\Uuid\Uuid;

class GetWebhookIntegrationMessagesQueryHandler implements QueryHandler
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
        return $query instanceof GetWebhookIntegrationMessagesQuery;
    }

    public function __invoke(GetWebhookIntegrationMessagesQuery $query)
    {
        if (!Uuid::isValid($query->getIntegrationId())) {
            throw new NotFoundException();
        }

        $integration = $this->webhookIntegrationReadModel->findById(IntegrationId::fromString($query->getIntegrationId()));

        if (!$integration) {
            throw new NotFoundException();
        }

        $projectId = $integration->getProjectId();

        if (!$this->projectMembershipChecker->isMember($projectId, $query->getIssuedBy())) {
            throw new UnauthorizedException();
        }

        $limit = 10;
        $offset = ($query->getPage() - 1) * $limit;

        $messages = $this->webhookIntegrationReadModel->findMessages(
            IntegrationId::fromString($query->getIntegrationId()),
            $query->getCriteria(),
            $query->getSort(),
            $limit,
            $offset
        );

        $count = $this->webhookIntegrationReadModel->findMessagesCount(
            IntegrationId::fromString($query->getIntegrationId()),
            $query->getCriteria()
        );

        return new PaginatedQueryResult(
            $messages,
            $count,
            $query->getPage()
        );
    }
}