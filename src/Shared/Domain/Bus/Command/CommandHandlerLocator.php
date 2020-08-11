<?php


namespace Overseer\Shared\Domain\Bus\Command;


interface CommandHandlerLocator
{
    function locateHandler(Command $command): CommandHandler;
}