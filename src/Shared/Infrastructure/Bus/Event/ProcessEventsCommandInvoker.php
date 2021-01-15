<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use Symfony\Component\Process\Process;

class ProcessEventsCommandInvoker
{
    private string $rootDir;
    private string $commandName;

    public function __construct(string $rootDir, string $commandName)
    {
        $this->rootDir = $rootDir;
        $this->commandName = $commandName;
    }

    public function invoke()
    {
        $process = new Process('bin/console ' . $this->commandName);
        $process->setWorkingDirectory($this->rootDir);
        $process->run();
    }
}