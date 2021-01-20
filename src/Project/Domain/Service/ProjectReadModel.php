<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Project\Domain\ValueObject\Username;

interface ProjectReadModel
{
    public function findBySlug(Slug $slug): ?Project;
    public function findById(ProjectId $projectId): ?Project;
    public function findByInvitationId(ProjectMemberInvitationId $invitationId): ?Project;
    public function findByProjectMemberId(ProjectMemberId $projectMemberId): ?Project;
    public function getProjects(string $issuedBy, array $criteria = [], array $sort = [], $limit = 10, int $offset = 0): array;
    public function count(string $issuedBy, array $criteria = []): int;
    public function findByApiKey(string $apiKey): ?Project;
    public function getProjectErrors(Project $project, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array;
    public function countProjectErrors(Project $project, array $criteria = []): int;
    public function getErrorsSummary(Project $project, array $criteria = []): array;
    public function findWhereUserIsAMember(Username $username): array;
}