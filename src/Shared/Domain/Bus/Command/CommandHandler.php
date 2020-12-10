<?php


namespace Overseer\Shared\Domain\Bus\Command;


interface CommandHandler
{
    public function handles(Command $command): bool;
}