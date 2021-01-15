<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Collection\Stacktrace;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ErrorId;
use Overseer\Project\Domain\ValueObject\Exception;

interface ErrorFactory
{
    public function createError(Project $project, ErrorId $errorId, \DateTime $occurredAt, Exception $exception, Stacktrace $stacktrace): Error;
    public function createStacktrace(array $stacktrace): Stacktrace;
    public function createException(string $class, string $errorCode, string $errorMessage, string $line, string $file);
}