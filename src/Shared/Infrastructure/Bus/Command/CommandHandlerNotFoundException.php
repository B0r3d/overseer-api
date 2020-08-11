<?php


namespace Overseer\Shared\Infrastructure\Bus\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CommandHandlerNotFoundException extends \RuntimeException
{
    function __construct(Command $command)
    {
        $message = 'Class "' . get_class($command) . 'Handler" is not defined or registered as a service';
        parent::__construct($message);
    }
}