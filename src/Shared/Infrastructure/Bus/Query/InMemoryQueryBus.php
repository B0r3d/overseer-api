<?php


namespace Overseer\Shared\Infrastructure\Bus\Query;


use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryBus;
use Overseer\Shared\Domain\Bus\Query\QueryHandlerLocator;
use Overseer\Shared\Domain\Bus\Query\Result;

final class InMemoryQueryBus implements QueryBus
{
    private QueryHandlerLocator $queryHandlerLocator;

    function __construct(QueryHandlerLocator $queryHandlerLocator)
    {
        $this->queryHandlerLocator = $queryHandlerLocator;
    }

    function ask(Query $query): Result
    {
        /** @var \Closure $queryHandler */
        $queryHandler = $this->queryHandlerLocator->locateHandler($query);
        return $queryHandler($query);
    }
}