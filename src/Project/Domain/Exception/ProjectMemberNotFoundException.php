<?php


namespace Overseer\Project\Domain\Exception;


use Throwable;

class ProjectMemberNotFoundException extends \RuntimeException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withProjectMemberId(string $projectMemberId)
    {
        $message = 'Project member with uuid "' . $projectMemberId . '" was not found';
        return new self($message);
    }
}