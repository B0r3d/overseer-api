<?php


namespace Overseer\User\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserWriteModel;

final class DoctrineUserWriteModel implements UserWriteModel
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}