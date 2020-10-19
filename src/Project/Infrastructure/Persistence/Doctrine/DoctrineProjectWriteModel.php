<?php


namespace Overseer\Project\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Service\ProjectWriteModel;

final class DoctrineProjectWriteModel implements ProjectWriteModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Project $project): void
    {
        $this->em->persist($project);
        $this->em->flush();
    }
}