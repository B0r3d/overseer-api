<?php


namespace Overseer\User\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class DoctrineUserReadModel implements UserReadModel, UserProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findOneByUsernameOrEmail(Username $username, Email $email): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.username.value = :username')
            ->orWhere('u.email.value = :email')
            ->setParameter('username', $username->value())
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUser(UserId $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'uuid.value' => $userId->value(),
        ]);
    }

    public function findOneByLogin(string $login): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.username.value = :login')
            ->orWhere('u.email.value = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function loadUserByUsername($username)
    {
        return $this->findOneByLogin($username);
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->findOneByLogin($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}