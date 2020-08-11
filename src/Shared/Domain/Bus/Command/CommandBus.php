<?php


namespace Overseer\Shared\Domain\Bus\Command;


interface CommandBus
{
    function dispatch(Command $command): void;
}