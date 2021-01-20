<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationQuery;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Ramsey\Uuid\Uuid;

class GetTelegramIntegrationQueryHandler implements QueryHandler
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
        return $query instanceof GetTelegramIntegrationQuery;
    }

    public function __invoke(GetTelegramIntegrationQuery $query)
    {
        if (!Uuid::isValid($query->getId())) {
            throw new NotFoundException();
        }

        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($query->getId()));

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