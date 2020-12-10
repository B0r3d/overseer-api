<?php


namespace Overseer\Project\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Project\Domain\ValueObject\ProjectMemberUsername;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Project\Domain\ValueObject\Username;

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
            'slug.value' => $slug->value(),
        ]);
    }

    public function findByUuid(ProjectId $projectId): ?Project
    {
        return $this->em->getRepository(Project::class)->findOneBy([
            'uuid.value' => $projectId->value(),
        ]);
    }

    public function findByInvitationIdWithGivenStatus(ProjectMemberInvitationId $invitationId, InvitationStatus $invitationStatus): ?Project
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('p')
           ->from(Project::class, 'p')
           ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p.id')
           ->where('pmi.uuid.value = :invitation_id_value')
           ->andWhere('pmi.status.value = :invitation_status_value')
           ->setParameter('invitation_id_value', $invitationId->value())
           ->setParameter('invitation_status_value', $invitationStatus->value())
           ->getQuery()
           ->getOneOrNullResult()
        ;
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

    public function getProjects(string $issuedBy, int $page = 1, array $criteria = [], array $sort = [], $limit = 10): array
    {
        $projectOwner = new ProjectOwner($issuedBy);
        $projectMember = new ProjectMemberUsername($issuedBy);
        $invitedMember = new Username($issuedBy);
        $eligibleStatus = new InvitationStatus(InvitationStatus::INVITED);

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p.id')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p.id')
        ;

        $qb->where('p.projectOwner.value = :project_owner_value')
            ->orWhere('pm.username.value = :project_member_value')
            ->orWhere('pmi.username.value = :invited_member_value AND pmi.status.value = :project_member_invitation_invited_status_value')
            ->setParameter('project_owner_value', $projectOwner->value())
            ->setParameter('project_member_value', $projectMember->value())
            ->setParameter('invited_member_value', $invitedMember->value())
            ->setParameter('project_member_invitation_invited_status_value', $eligibleStatus->value())
        ;

        $items = $qb
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult()
        ;

        return $qb->getQuery()->getResult();
    }

    public function count(string $issuedBy, array $criteria = []): int
    {
        $projectOwner = new ProjectOwner($issuedBy);
        $projectMember = new ProjectMemberUsername($issuedBy);
        $invitedMember = new Username($issuedBy);
        $eligibleStatus = new InvitationStatus(InvitationStatus::INVITED);

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(p.id) as items_count')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p.id')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p.id')
        ;

        $qb->where('p.projectOwner.value = :project_owner_value')
            ->orWhere('pm.username.value = :project_member_value')
            ->orWhere('pmi.username.value = :invited_member_value AND pmi.status.value = :project_member_invitation_invited_status_value')
            ->setParameter('project_owner_value', $projectOwner->value())
            ->setParameter('project_member_value', $projectMember->value())
            ->setParameter('invited_member_value', $invitedMember->value())
            ->setParameter('project_member_invitation_invited_status_value', $eligibleStatus->value())
        ;

        $data = $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $data['items_count'];
    }
}