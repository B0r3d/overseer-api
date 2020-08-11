<?php


namespace Overseer\Shared\Infrastructure\Bus\Query;


use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryBus;
use Overseer\Shared\Domain\Bus\Query\QueryHandlerLocator;
use Overseer\Shared\Domain\Bus\Query\Response;

final class InMemoryQueryBus implements QueryBus
{
    private QueryHandlerLocator $queryHandlerLocator;

    function __construct(QueryHandlerLocator $queryHandlerLocator)
    {
        $this->queryHandlerLocator = $queryHandlerLocator;
    }

    function ask(Query $query): ?Response
    {
        /** @var \Closure $queryHandler mark it as closure so it is not marked as invalid code */
        $queryHandler = $this->queryHandlerLocator->locateHandler($query);
        return $queryHandler($query);
    }
}