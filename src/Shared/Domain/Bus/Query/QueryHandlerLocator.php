<?php


namespace Overseer\Shared\Domain\Bus\Query;


interface QueryHandlerLocator
{
    function locateHandler(Query $query): QueryHandler;
}