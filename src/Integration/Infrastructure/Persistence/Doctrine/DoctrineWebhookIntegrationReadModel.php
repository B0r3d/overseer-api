<?php


namespace Overseer\Integration\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Entity\WebhookIntegrationMessage;
use Overseer\Integration\Domain\Enum\MessageStatus;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

class DoctrineWebhookIntegrationReadModel implements WebhookIntegrationReadModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function findById(IntegrationId $id): ?WebhookIntegration
    {
        return $this->em->getRepository(WebhookIntegration::class)->find($id->value());
    }

    public function findAllByProjectId(Uuid $projectId): array
    {
        return $this->em->getRepository(WebhookIntegration::class)->findBy([
            'projectId' => $projectId,
        ]);
    }

    public function findUnprocessedMessages(): array
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('wim')
            ->from(WebhookIntegration::class, 'wi')
            ->leftJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wim.status = :status')
            ->setParameter('status', MessageStatus::UNPROCESSED())
            ->getQuery()
            ->getResult();
    }

    public function findUnprocessedMessagesCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('COUNT(wim.id) as message_count')
            ->from(WebhookIntegration::class, 'wi')
            ->leftJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wim.status = :status')
            ->setParameter('status', MessageStatus::UNPROCESSED())
            ->getQuery()
            ->getOneOrNullResult();

        return $result['message_count'];
    }

    public function findFailedMessages(): array
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('wim')
            ->from(WebhookIntegration::class, 'wi')
            ->leftJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wim.status = :status')
            ->andWhere('wim.nextAttempt <= :now')
            ->andWhere('wim.attemptCount < 25')
            ->setParameter('status', MessageStatus::ERROR())
            ->setParameter('now', new \DateTime())
            ->orderBy('wim.nextAttempt')
            ->getQuery()
            ->getResult();
    }

    public function findFailedMessagesCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('COUNT(wim.id) as message_count')
            ->from(WebhookIntegration::class, 'wi')
            ->leftJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wim.status = :status')
            ->andWhere('wim.nextAttempt <= :now')
            ->andWhere('wim.attemptCount <= 25')
            ->setParameter('status', MessageStatus::ERROR())
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();

        return $result['message_count'];
    }

    public function findIntegrations(Uuid $projectId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('wi')
            ->from(WebhookIntegration::class, 'wi')
            ->where('wi.projectId = :project_id')
            ->setParameter('project_id', $projectId->value())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('wi.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findIntegrationsCount(Uuid $projectId, array $criteria = []): int
    {
        $qb = $this->em->createQueryBuilder();

        $result = $qb->select('COUNT(wi.id) as integrations_count')
            ->from(WebhookIntegration::class, 'wi')
            ->where('wi.projectId = :project_id')
            ->setParameter('project_id', $projectId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['integrations_count'];
    }

    public function findMessages(IntegrationId $integrationId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('wim')
            ->from(WebhookIntegration::class, 'wi')
            ->innerJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wi = :integration_id')
            ->setParameter('integration_id', $integrationId->value())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('wim.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMessagesCount(IntegrationId $integrationId, array $criteria = [])
    {
        $qb = $this->em->createQueryBuilder();

        $result = $qb->select('COUNT(wim.id) as webhook_integration_messages_count')
            ->from(WebhookIntegration::class, 'wi')
            ->innerJoin(WebhookIntegrationMessage::class, 'wim', 'WITH', 'wim.integration = wi')
            ->where('wi = :integration_id')
            ->setParameter('integration_id', $integrationId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['webhook_integration_messages_count'];
    }
}