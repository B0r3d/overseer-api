<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\Shared\Domain\Bus\Event\EventSubscriberLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SimpleSubscriberLocator implements EventSubscriberLocator
{
    private array $eventSubscribers;

    public function __construct(iterable $eventSubscribers)
    {
        $this->eventSubscribers = [];
        foreach($eventSubscribers as $subscriber) {
            $this->eventSubscribers[] = $subscriber;
        }
    }

    public function locateEventSubscribers(DomainEvent $event): array
    {
        $eventSubscribers = [];

        /** @var EventSubscriber $subscriber */
        foreach($this->eventSubscribers as $subscriber) {
            if (in_array(get_class($event), $subscriber::getSubscribedDomainEvents())) {
                $eventSubscribers[] = $subscriber;
            }
        }

        return $eventSubscribers;
    }
}