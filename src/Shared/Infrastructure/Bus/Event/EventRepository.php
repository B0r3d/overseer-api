<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Doctrine\ORM\EntityManagerInterface;

class EventRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function findUnprocessedEvents(): array
    {
        return $this->em->getRepository(EventEntity::class)->findBy([
            'status' => EventStatus::UNPROCESSED()
        ], [
            'occurredAt' => 'ASC'
        ], 1);
    }

    public function findFailedEvents()
    {

    }

    public function findUnprocessedEventsCount()
    {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('COUNT(ee.id) as event_count')
            ->from(EventEntity::class, 'ee')
            ->where('ee.status = :unprocessed')
            ->setParameter('unprocessed', EventStatus::UNPROCESSED())
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result['event_count'];
    }

    public function findFailedEventsCount()
    {

    }

    public function findProcessedEventsCount()
    {

    }

    public function saveEvent(EventEntity $entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}