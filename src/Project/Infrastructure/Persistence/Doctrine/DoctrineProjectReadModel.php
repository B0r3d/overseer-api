<?php


namespace Overseer\Project\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Project\Domain\Entity\ApiKey;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
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
        $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p')
            ->where('pmi.username.value = :issued_by AND pmi.status.value = :invited_status')
            ->orWhere('pm.username.value = :issued_by')
            ->setParameter('issued_by', $issuedBy)
            ->setParameter('invited_status', $invitedStatus->getValue());

        $this->addGetProjectsQueryCriteria($qb, $criteria);

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function count(string $issuedBy, array $criteria = []): int
    {
        $invitedStatus = InvitationStatus::INVITED();
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(p.id) as project_count')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMemberInvitation::class, 'pmi', 'WITH', 'pmi.project = p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p')
            ->where('pmi.username.value = :issued_by AND pmi.status.value = :invited_status')
            ->orWhere('pm.username.value = :issued_by')
            ->setParameter('issued_by', $issuedBy)
            ->setParameter('invited_status', $invitedStatus->getValue());

        $this->addGetProjectsQueryCriteria($qb, $criteria);

        $result = $qb->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['project_count'];
    }

    public function findByApiKey(string $apiKey): ?Project
    {
        $qb = $this->em->createQueryBuilder();
        return $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ApiKey::class, 'ak', 'WITH', 'ak.project = p')
            ->where('ak.value = :api_key')
            ->setParameter('api_key', $apiKey)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getProjectErrors(Project $project, array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('pe')
            ->from(Project::class, 'p')
            ->leftJoin(Error::class, 'pe', 'WITH', 'pe.project = p')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->orderBy('pe.occurredAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (array_key_exists('occurred_at', $sort)) {
            if ($sort['occurred_at'] === 'ASC') {
                $qb->orderBy('pe.occurredAt', 'ASC');
            } else {
                $qb->orderBy('pe.occurredAt', 'DESC');
            }
        }

        $this->addGetErrorsQueryCriteria($qb, $criteria);

        return $qb->getQuery()
            ->getResult()
        ;
    }

    public function countProjectErrors(Project $project, array $criteria = []): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(pe.id) as error_count')
            ->from(Project::class, 'p')
            ->leftJoin(Error::class, 'pe', 'WITH', 'pe.project = p')
            ->where('p = :project')
            ->setParameter('project', $project);

        $this->addGetErrorsQueryCriteria($qb, $criteria);

        $result = $qb->getQuery()
            ->getOneOrNullResult()
        ;

        return $result['error_count'];
    }

    public function getErrorsSummary(Project $project, array $criteria = []): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(pe.id) as error_count')
            ->addSelect('YEAR(pe.occurredAt) as e_year')
            ->addSelect('MONTH(pe.occurredAt) as e_month')
            ->addSelect('DAY(pe.occurredAt) as e_day')
            ->from(Project::class, 'p')
            ->leftJoin(Error::class, 'pe', 'WITH', 'pe.project = p')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->orderBy('pe.occurredAt', 'ASC')
            ->groupBy('e_year')
            ->addGroupBy('e_month')
            ->addGroupBy('e_day');

        $this->addGetErrorsQueryCriteria($qb, $criteria);

        $result = $qb->getQuery()
            ->getResult()
        ;

        $finalResult = [];
        foreach ($result as $record) {
            $dateString = $record['e_day'] . '-' . $record['e_month'] . '-' . $record['e_year'];
            $finalResult[] = [
                'error_count' => $record['error_count'],
                'date' => (new \DateTime($dateString))->format(\DateTime::ISO8601),
            ];
        }

        return $finalResult;
    }

    private function addGetProjectsQueryCriteria(\Doctrine\ORM\QueryBuilder $qb, array $criteria)
    {
        if (!empty($criteria['search'])) {
            $qb->andWhere('
                (p.projectTitle.value LIKE :phrase OR p.projectTitle.value IN(:fuzzy_query)) OR
                (p.description LIKE :phrase OR p.description IN(:fuzzy_query))
            ')
                ->setParameter('phrase', '%' . $criteria['search'] . '%')
                ->setParameter('fuzzy_query', explode('/\s+/', $criteria['search']));
        }

        if (!empty($criteria['slug'])) {
            $qb->andWhere('p.slug.value = :slug')
                ->setParameter('slug', $criteria['slug']);
        }
    }

    private function addGetErrorsQueryCriteria(\Doctrine\ORM\QueryBuilder $qb, array $criteria)
    {
        if (!empty($criteria['search'])) {
            $qb->andWhere('pe.exception.class LIKE :phrase OR pe.exception.class IN(:fuzzy_query)')
                ->setParameter('phrase', '%' . $criteria['search'] . '%')
                ->setParameter('fuzzy_query', explode('/\s+/', $criteria['search']));
        }

        if (!empty($criteria['date_from']) && $criteria['date_from'] instanceof \DateTime) {
            $qb->where('pe.occurredAt >= :date_from')
                ->setParameter('date_from', $criteria['date_from']);
        }

        if (!empty($criteria['date_to']) && $criteria['date_to'] instanceof \DateTime) {
            $qb->where('pe.occurredAt <= :date_to')
                ->setParameter('date_to', $criteria['date_to']);
        }
    }

    public function findWhereUserIsAMember(Username $username): array
    {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('p')
            ->from(Project::class, 'p')
            ->leftJoin(ProjectMember::class, 'pm', 'WITH', 'pm.project = p')
            ->where('pm.username.value = :username')
            ->setParameter('username', $username->getValue())
            ->getQuery()
            ->getResult()
        ;
    }
}