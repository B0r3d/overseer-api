<?php


namespace Overseer\Shared\Domain\Bus\Event;


interface EventSubscriber
{
    static function getSubscribedDomainEvents(): array;
}