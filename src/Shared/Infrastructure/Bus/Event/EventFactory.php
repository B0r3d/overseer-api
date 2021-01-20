<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

class EventFactory
{
    public function recreateEvent(EventEntity $entity): DomainEvent
    {
        $class = $entity->getClass();
        return $class::fromPrimitives(
            $entity->getAggregateId(),
            $entity->getPayload(),
            $entity->getId(),
            $entity->getOccurredAt()->getTimestamp()
        );
    }
}