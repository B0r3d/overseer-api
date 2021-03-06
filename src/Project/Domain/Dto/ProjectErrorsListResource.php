<?php


namespace Overseer\Project\Domain\Dto;


use Overseer\Project\Domain\Entity\Error;

class ProjectErrorsListResource implements \JsonSerializable
{
    private string $id;
    private string $occurredAt;
    private string $class;
    private string $errorCode;
    private string $errorMessage;
    private int $line;
    private string $file;

    public function __construct(Error $error)
    {
        $this->id = $error->getId()->value();
        $this->occurredAt = $error->getOccurredAt()->format(\DateTime::ISO8601);
        $this->class = $error->getException()->getClass();
        $this->errorCode = $error->getException()->getErrorCode();
        $this->errorMessage = $error->getException()->getErrorMessage();
        $this->line = $error->getException()->getLine();
        $this->file = $error->getException()->getFile();
    }


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'occurred_at' => $this->occurredAt,
            'class' => $this->class,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
            'line' => $this->line,
            'file' => $this->file,
        ];
    }
}