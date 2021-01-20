<?php


namespace Overseer\Integration\Application\Query;


use Overseer\Integration\Application\ProjectMembershipChecker;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationMessagesQuery;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Ramsey\Uuid\Uuid;

class GetTelegramIntegrationMessagesQueryHandler implements QueryHandler
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
        return $query instanceof GetTelegramIntegrationMessagesQuery;
    }

    public function __invoke(GetTelegramIntegrationMessagesQuery $query)
    {
        if (!Uuid::isValid($query->getIntegrationId())) {
            throw new NotFoundException();
        }

        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($query->getIntegrationId()));

        if (!$integration) {
            throw new NotFoundException();
        }

        $projectId = $integration->getProjectId();

        if (!$this->projectMembershipChecker->isMember($projectId, $query->getIssuedBy())) {
            throw new UnauthorizedException();
        }

        $limit = 10;
        $offset = ($query->getPage() - 1) * $limit;

        $messages = $this->telegramIntegrationReadModel->findMessages(
            IntegrationId::fromString($query->getIntegrationId()),
            $query->getCriteria(),
            $query->getSort(),
            $limit,
            $offset
        );

        $count = $this->telegramIntegrationReadModel->findMessagesCount(
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