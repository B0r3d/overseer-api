<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Throwable;

final class ProjectMemberInvitationNotFoundException extends \RuntimeException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withUuid(ProjectMemberInvitationId $invitationId)
    {
        $message = 'ProjectMemberInvitation not found with UUID "' . $invitationId->value() . '"';
        return new self($message);
    }
}