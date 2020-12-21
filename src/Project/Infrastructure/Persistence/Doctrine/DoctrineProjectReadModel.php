<?php


namespace Overseer\Project\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\Slug;

final class DoctrineProjectReadModel implements ProjectReadModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findBySlug(Slug $slug): ?Project
    {
        return $this->em->getRepository(Project::class)->findOneBy([
            'slug.value' => $slug->getValue(),
        ]);
    }

    public function findByProjectMemberId(ProjectMemberId $projectMemberId): ?Project
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p.id')
            ->where('pm.uuid.value = :project_member_id_value')
            ->setParameter('project_member_id_value', $projectMemberId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findById(ProjectId $projectId): ?Project
    {
        return $this->em->getRepository(Project::class)->find($projectId->value());
    }

    public function findByInvitationId(ProjectMemberInvitationId $invitationId): ?Project
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p')
            ->where('pmi.id = :invitation_id')
            ->setParameter('invitation_id', $invitationId->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getProjects(string $issuedBy, array $criteria = [], array $sort = [], $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();
        $invitedStatus = InvitationStatus::INVITED();
        return $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p')
            ->where('pmi.username.value = :issued_by AND pmi.status.value = :invited_status')
            ->orWhere('pm.username.value = :issued_by')
            ->setParameter('issued_by', $issuedBy)
            ->setParameter('invited_status', $invitedStatus->getValue())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function count(string $issuedBy, array $criteria = []): int
    {
        $invitedStatus = InvitationStatus::INVITED();
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('count(p.id) as project_count')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p')
            ->where('pmi.username.value = :issued_by AND pmi.status.value = :invited_status')
            ->orWhere('pm.username.value = :issued_by')
            ->setParameter('issued_by', $issuedBy)
            ->setParameter('invited_status', $invitedStatus->getValue())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['project_count'];
    }
}