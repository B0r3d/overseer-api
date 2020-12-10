<?php


namespace Overseer\Shared\Infrastructure\Bus\Command;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Command\CommandHandlerLocator;

final class SimpleCommandHandlerLocator implements CommandHandlerLocator
{
    private array $commandHandlers;

    public function __construct(iterable $commandHandlers)
    {
        $this->commandHandlers = [];

        foreach($commandHandlers as $handler) {
            $this->commandHandlers[] = $handler;
        }
    }

    function locateHandler(Command $command): CommandHandler
    {
        /** @var CommandHandler $handler */
        foreach($this->commandHandlers as $handler) {
            if($handler->handles($command)) {
                return $handler;
            }
        }

        throw new CommandHandlerNotFoundException($command);
    }
}