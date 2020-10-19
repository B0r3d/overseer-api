<?php


namespace Overseer\Project\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Service\ProjectReadModel;
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
}