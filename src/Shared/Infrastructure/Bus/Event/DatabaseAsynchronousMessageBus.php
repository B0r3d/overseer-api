<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;
use Overseer\Shared\Domain\Bus\Event\EventBus;

class DatabaseAsynchronousMessageBus implements EventBus
{
    private EventRepository $eventRepository;
    private ProcessEventsCommandInvoker $invoker;

    public function __construct(EventRepository $eventRepository, ProcessEventsCommandInvoker $invoker)
    {
        $this->eventRepository = $eventRepository;
        $this->invoker = $invoker;
    }

    function publish(DomainEvent ...$domainEvents): void
    {
        foreach($domainEvents as $domainEvent) {
            $entity = new EventEntity(
                $domainEvent->eventId(),
                $domainEvent->aggregateId(),
                $domainEvent->occurredAt(),
                $domainEvent->toPrimitives(),
                get_class($domainEvent)
            );

            $this->eventRepository->saveEvent($entity);
        }

        $this->invoker->invoke();
    }
}