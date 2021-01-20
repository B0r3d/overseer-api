<?php


namespace Overseer\Project\Domain\ValueObject;


class Exception
{
    private string $class;
    private string $errorCode;
    private string $errorMessage;
    private int $line;
    private string $file;

    public function __construct(string $class, string $errorCode, string $errorMessage, int $line, string $file)
    {
        $this->class = $class;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->line = $line;
        $this->file = $file;
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

}