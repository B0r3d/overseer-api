<?php


namespace Overseer\User\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\User\Domain\Entity\Session;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\JsonWebToken;
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

    public function findUser(UserId $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'id' => $userId->value(),
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

    public function findUserByUsername(Username $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'username.value' => $username->getValue(),
        ]);
    }

    public function findfindUserByUserByEmail(Email $email)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'email.value' => $email->getValue(),
        ]);
    }

    public function findUserByRefreshToken(JsonWebToken $token): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('u')
            ->from(User::class, 'u')
            ->leftJoin(Session::class, 's', 'WITH', 's.user = u')
            ->where('s.refreshToken = :token')
            ->setParameter('token', $token->getToken())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUserByPasswordResetToken(string $passwordResetTokenId): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.passwordResetToken.id = :password_reset_token')
            ->setParameter('password_reset_token', $passwordResetTokenId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getUsers(array $criteria = [], array $sort = [], int $limit = 10, int $offset = 0): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('u')
            ->from(User::class, 'u')
        ;

        if (isset($criteria['search']) && $criteria['search']) {
            $qb->where('u.username.value LIKE :search')
                ->setParameter('search', '%' . $criteria['search'] . '%')
            ;
        }

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function count(array $criteria = [])
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('count(u.id) as user_count')
            ->from(User::class, 'u')
        ;

        if (isset($criteria['search']) && $criteria['search']) {
            $qb->where('u.username.value LIKE :search')
                ->setParameter('search', '%' . $criteria['search'] . '%')
            ;
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['user_count'];
    }

    public function findUserByEmail(Email $email): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.email.value = :email')
            ->setParameter('email', $email->getValue())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}