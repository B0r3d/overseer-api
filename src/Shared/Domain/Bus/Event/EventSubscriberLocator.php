<?php


namespace Overseer\Shared\Domain\Bus\Event;


interface EventSubscriberLocator
{
    function locateEventSubscribers(DomainEvent $event): array;
}