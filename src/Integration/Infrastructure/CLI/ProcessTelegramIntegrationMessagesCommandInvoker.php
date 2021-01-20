<?php


namespace Overseer\Integration\Infrastructure\CLI;


use Symfony\Component\Process\Process;

class ProcessTelegramIntegrationMessagesCommandInvoker
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