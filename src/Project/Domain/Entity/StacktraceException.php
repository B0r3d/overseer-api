<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\ValueObject\Exception;

class StacktraceException
{
    private Error $error;
    private ?int $id;
    private Exception $exception;

    public function __construct(Error $error, Exception $exception)
    {
        $this->error = $error;
        $this->exception = $exception;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getException(): Exception
    {
        return $this->exception;
    }
}