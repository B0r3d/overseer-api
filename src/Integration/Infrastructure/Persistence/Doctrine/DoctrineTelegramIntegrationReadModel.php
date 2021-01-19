<?php


namespace Overseer\Integration\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Entity\TelegramIntegrationMessage;
use Overseer\Integration\Domain\Enum\MessageStatus;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

class DoctrineTelegramIntegrationReadModel implements TelegramIntegrationReadModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(IntegrationId $id): ?TelegramIntegration
    {
        return $this->em->getRepository(TelegramIntegration::class)->find($id->value());
    }

    public function findAllByProjectId(Uuid $projectId): array
    {
        return $this->em->getRepository(TelegramIntegration::class)->findBy([
            'projectId' => $projectId,
        ]);
    }

    public function findIntegrations(Uuid $projectId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('ti')
            ->from(TelegramIntegration::class, 'ti')
            ->where('ti.projectId = :project_id')
            ->setParameter('project_id', $projectId->value())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('ti.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findIntegrationsCount(Uuid $projectId, array $criteria = []): int
    {
        $qb = $this->em->createQueryBuilder();

        $result = $qb->select('COUNT(ti.id) as integrations_count')
            ->from(TelegramIntegration::class, 'ti')
            ->where('ti.projectId = :project_id')
            ->setParameter('project_id', $projectId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['integrations_count'];
    }

    public function findMessages(IntegrationId $integrationId, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('tim')
            ->from(TelegramIntegration::class, 'ti')
            ->innerJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('ti = :integration_id')
            ->setParameter('integration_id', $integrationId->value())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('tim.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMessagesCount(IntegrationId $integrationId, array $criteria = []): int
    {
        $qb = $this->em->createQueryBuilder();

        $result = $qb->select('COUNT(tim.id) as telegram_integration_messages_count')
            ->from(TelegramIntegration::class, 'ti')
            ->innerJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('ti = :integration_id')
            ->setParameter('integration_id', $integrationId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['telegram_integration_messages_count'];
    }

    public function findUnprocessedMessages(): array
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('tim')
            ->from(TelegramIntegration::class, 'ti')
            ->leftJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('tim.status = :status')
            ->setParameter('status', MessageStatus::UNPROCESSED())
            ->getQuery()
            ->getResult();
    }

    public function findUnprocessedMessagesCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('COUNT(tim.id) as message_count')
            ->from(TelegramIntegration::class, 'ti')
            ->leftJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('tim.status = :status')
            ->setParameter('status', MessageStatus::UNPROCESSED())
            ->getQuery()
            ->getOneOrNullResult();

        return $result['message_count'];
    }

    public function findFailedMessages(): array
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('tim')
            ->from(TelegramIntegration::class, 'ti')
            ->leftJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('tim.status = :status')
            ->andWhere('tim.nextAttempt <= :now')
            ->andWhere('tim.attemptCount < 25')
            ->setParameter('status', MessageStatus::ERROR())
            ->setParameter('now', new \DateTime())
            ->orderBy('tim.nextAttempt')
            ->getQuery()
            ->getResult();
    }

    public function findFailedMessagesCount(): int
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('COUNT(tim.id) as message_count')
            ->from(TelegramIntegration::class, 'ti')
            ->leftJoin(TelegramIntegrationMessage::class, 'tim', 'WITH', 'tim.integration = ti')
            ->where('tim.status = :status')
            ->andWhere('tim.nextAttempt <= :now')
            ->andWhere('tim.attemptCount <= 25')
            ->setParameter('status', MessageStatus::ERROR())
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();

        return $result['message_count'];
    }
}