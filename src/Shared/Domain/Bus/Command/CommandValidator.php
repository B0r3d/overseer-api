<?php


namespace Overseer\Shared\Domain\Bus\Command;


interface CommandValidator
{
    public function validate(Command $command);
}