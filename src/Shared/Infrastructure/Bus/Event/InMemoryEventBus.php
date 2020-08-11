<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Domain\Bus\Event\EventSubscriberLocator;

final class InMemoryEventBus implements EventBus
{
    private EventSubscriberLocator $eventSubscriberLocator;

    function __construct(EventSubscriberLocator $eventSubscriberLocator)
    {
        $this->eventSubscriberLocator = $eventSubscriberLocator;
    }

    function publish(DomainEvent ...$domainEvents): void
    {
        foreach($domainEvents as $event) {
            $subscribers = $this->eventSubscriberLocator->locateEventSubscribers($event);
            foreach($subscribers as $subscriber) {
                $subscriber($event);
            }
        }
    }
}