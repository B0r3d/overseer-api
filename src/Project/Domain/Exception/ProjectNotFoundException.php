<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Exception\NotFoundException;

final class ProjectNotFoundException extends NotFoundException
{
    public static function withUuid(ProjectId $projectId)
    {
        $message = 'Project with uuid "' . $projectId->value() . '" was not found.';
        return new self($message);
    }

    public static function withProjectMemberInvitationId(string $invitationId)
    {
        $message = 'Project with invitationId "' . $invitationId . '" was not found.';
        return new self($message);
    }

    public static function withProjectMemberId(string $projectMemberId)
    {
        $message = 'Project with projectMemberId "' . $projectMemberId . '" was not found';
        return new self($message);
    }
}