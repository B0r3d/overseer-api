<?php


namespace Overseer\Shared\Domain\Bus\Event;


interface EventBus
{
    function publish(DomainEvent ...$domainEvents): void;
}