<?php


namespace Overseer\Shared\Domain\Bus\Query;


interface QueryBus
{
    function ask(Query $query): ?Response;
}