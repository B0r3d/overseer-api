<?php


namespace Overseer\Shared\Infrastructure\Bus\Command;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Overseer\Shared\Domain\Bus\Command\CommandHandlerLocator;

final class InMemoryCommandBus implements CommandBus
{
    private CommandHandlerLocator $commandHandlerLocator;
    private array $handlers;

    public function __construct(CommandHandlerLocator $commandHandlerLocator)
    {
        $this->commandHandlerLocator = $commandHandlerLocator;
        $this->handlers = [];
    }

    function dispatch(Command $command): void
    {
        $commandClass = get_class($command);
        if (empty($this->handlers[$commandClass])) {
            $this->handlers[$commandClass] = $this->commandHandlerLocator->locateHandler($command);
        }

        $this->handlers[$commandClass]($command);
    }
}