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
}