<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Slug;

interface ProjectReadModel
{
    public function findBySlug(Slug $slug): ?Project;
    public function findById(ProjectId $projectId): ?Project;
    public function findByInvitationId(ProjectMemberInvitationId $invitationId): ?Project;
    public function findByProjectMemberId(ProjectMemberId $projectMemberId): ?Project;
    public function getProjects(string $issuedBy, array $criteria = [], array $sort = [], $limit = 10, int $offset = 0): array;
    public function count(string $issuedBy, array $criteria = []): int;
}