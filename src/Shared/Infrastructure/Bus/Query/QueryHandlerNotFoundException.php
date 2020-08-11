<?php


namespace Overseer\Shared\Infrastructure\Bus\Query;


use Overseer\Shared\Domain\Bus\Query\Query;

final class QueryHandlerNotFoundException extends \RuntimeException
{
    public function __construct(Query $query)
    {
        $message = 'Class "' . get_class($query) . 'Handler" is not defined or registered as a service';
        parent::__construct($message);
    }
}