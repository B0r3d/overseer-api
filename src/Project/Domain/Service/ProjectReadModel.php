<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Slug;

interface ProjectReadModel
{
    public function findBySlug(Slug $slug): ?Project;
    public function findByUuid(ProjectId $projectId): ?Project;
    public function findByInvitationIdWithGivenStatus(ProjectMemberInvitationId $invitationId, InvitationStatus $invitationStatus): ?Project;
    public function findByProjectMemberId(ProjectMemberId $projectMemberId): ?Project;
}