<?php


namespace Overseer\User\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

class DoctrineUserReadModel implements UserReadModel
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function findOneByUsernameAndEmail(Username $username, Email $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'username.value' => $username->value(),
            'email.value' => $email->value(),
        ]);
    }

    function findUser(UserId $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'uuid.value' => $userId->value(),
        ]);
    }
}