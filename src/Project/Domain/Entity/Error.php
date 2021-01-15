<?php


namespace Overseer\Project\Domain\Entity;


use Overseer\Project\Domain\Collection\Stacktrace;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\Exception;

class Error
{
    private Project $project;
    private string $id;
    private ErrorId $_id;
    private \DateTime $occurredAt;
    private Exception $exception;
    private $stacktrace;
    private Stacktrace $_stacktrace;

    public function __construct(Project $project, ErrorId $id, \DateTime $occurredAt, Exception $exception, Stacktrace $stacktrace)
    {
        $this->project = $project;
        $this->id = $id->value();
        $this->occurredAt = $occurredAt;
        $this->exception = $exception;
        $this->_stacktrace = $stacktrace;
        $this->stacktrace = [];

        foreach ($stacktrace as $exception) {
            $this->stacktrace[] = new StacktraceException($this, $exception);
        }
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getId(): ErrorId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = ErrorId::fromString($this->id);
        return $this->_id;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }

    public function getException(): Exception
    {
        return $this->exception;
    }

    public function getStacktrace(): Stacktrace
    {
        if (isset($this->_stacktrace)) {
            return $this->_stacktrace;
        }

        $stacktrace = new Stacktrace();

        /** @var StacktraceException $stacktraceException */
        foreach ($this->stacktrace as $stacktraceException) {
            $stacktrace->addException($stacktraceException->getException());
        }

        $this->_stacktrace = $stacktrace;
        return $this->_stacktrace;
    }
}