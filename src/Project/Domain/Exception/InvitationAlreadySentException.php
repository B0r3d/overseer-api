<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Username;
use Throwable;

final class InvitationAlreadySentException extends \RuntimeException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withUsername(Username $username)
    {
        $message = 'Invitation already sent to user "'. $username->value() . '"';
        return new self($message);
    }

    public static function withUuid(ProjectMemberInvitationId $projectMemberInvitationId)
    {
        $message = 'Invitation "' . $projectMemberInvitationId->value() .'" already sent';
        return new self($message);
    }
}