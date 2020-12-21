<?php


namespace Overseer\Shared\Infrastructure\Bus\Query;


use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\Bus\Query\QueryHandlerLocator;

final class SimpleQueryHandlerLocator implements QueryHandlerLocator
{
    private array $queryHandlers;

    public function __construct(iterable $queryHandlers)
    {
        $this->queryHandlers = [];
        foreach($queryHandlers as $handler) {
            $this->queryHandlers[] = $handler;
        }
    }

    function locateHandler(Query $query): QueryHandler
    {
        /** @var QueryHandler $handler */
        foreach($this->queryHandlers as $handler) {
            if ($handler->handles($query)) {
                return $handler;
            }
        }

        throw new QueryHandlerNotFoundException($query);
    }
}