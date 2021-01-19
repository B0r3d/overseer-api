<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationQuery;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Ramsey\Uuid\Uuid;

class GetWebhookIntegrationQueryHandler implements QueryHandler
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
        return $query instanceof GetWebhookIntegrationQuery;
    }

    public function __invoke(GetWebhookIntegrationQuery $query)
    {
        if (!Uuid::isValid($query->getId())) {
            throw new NotFoundException();
        }

        $integration = $this->webhookIntegrationReadModel->findById(IntegrationId::fromString($query->getId()));

        if (!$integration) {
            throw new NotFoundException();
        }

        $projectId = $integration->getProjectId();

        if (!$this->projectMembershipChecker->isMember($projectId, $query->getIssuedBy())) {
            throw new UnauthorizedException();
        }

        return new SingleObjectResult($integration);
    }
}