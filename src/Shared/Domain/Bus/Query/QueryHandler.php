<?php


namespace Overseer\Shared\Domain\Bus\Query;


interface QueryHandler
{
    public function handles(Query $query): bool;
}