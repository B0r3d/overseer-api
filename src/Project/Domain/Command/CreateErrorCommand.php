<?php


namespace Overseer\Project\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CreateErrorCommand implements Command
{
    private string $projectId;
    private string $errorId;
    private string $class;
    private string $errorCode;
    private string $errorMessage;
    private int $line;
    private string $file;
    private string $occurredAt;
    private array $stacktrace;

    public function __construct(string $projectId, string $errorId, string $class, string $errorCode, string $errorMessage, int $line, string $file, string $occurredAt, array $stacktrace = [])
    {
        $this->projectId = $projectId;
        $this->errorId = $errorId;
        $this->class = $class;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->line = $line;
        $this->file = $file;
        $this->occurredAt = $occurredAt;
        $this->stacktrace = $stacktrace;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getErrorId(): string
    {
        return $this->errorId;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getOccurredAt(): string
    {
        return $this->occurredAt;
    }

    public function getStacktrace(): array
    {
        return $this->stacktrace;
    }
}