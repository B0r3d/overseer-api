<?php


namespace Overseer\Project\Application;


use Overseer\Project\Domain\Collection\Stacktrace;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Exception\InvalidStacktraceException;
use Overseer\Project\Domain\Service\ErrorFactory;
use Overseer\Project\Domain\Validator\ValidStacktrace;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\Exception;

class SimpleErrorFactory implements ErrorFactory
{

    public function createError(Project $project, ErrorId $errorId, \DateTime $occurredAt, Exception $exception, Stacktrace $stacktrace): Error
    {
        return new Error(
            $project,
            $errorId,
            $occurredAt,
            $exception,
            $stacktrace
        );
    }

    public function createStacktrace(array $stacktrace): Stacktrace
    {
        $stacktraceObject = new Stacktrace();

        $specification = new ValidStacktrace();

        if (!$specification->isSatisfiedBy($stacktrace)) {
            throw new InvalidStacktraceException();
        }

        foreach ($stacktrace as $exception) {
            $stacktraceObject->addException($this->createException(
                $exception['exception_class'],
                isset($exception['error_code']) ? $exception['error_code'] : '0',
                isset($exception['error_message']) ? $exception['error_message'] : '',
                $exception['line'],
                $exception['file']
            ));
        }

        return $stacktraceObject;
    }

    public function createException(string $class, string $errorCode, string $errorMessage, string $line, string $file)
    {
        return new Exception(
            $class,
            $errorCode,
            $errorMessage,
            $line,
            $file
        );
    }
}