<?php


namespace Overseer\User\Domain\Entity;


use Overseer\Shared\Domain\Aggregate\AggregateRoot;
use Overseer\Shared\Domain\ValueObject\ExpiryDate;
use Overseer\User\Domain\Collection\Sessions;
use Overseer\User\Domain\Enum\UserRole;
use Overseer\User\Domain\Event\UserDemoted;
use Overseer\User\Domain\Event\UserPasswordChanged;
use Overseer\User\Domain\Event\UserPasswordResetRequested;
use Overseer\User\Domain\Event\UserPromoted;
use Overseer\User\Domain\Event\UserRegistered;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\HashedPassword;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Overseer\User\Domain\ValueObject\PasswordResetToken;
use Overseer\User\Domain\ValueObject\PlainPassword;
use Overseer\User\Domain\ValueObject\Roles;
use Overseer\User\Domain\ValueObject\SessionId;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

class User extends AggregateRoot
{
    private UserId $_id;
    private string $id;
    private Username $username;
    private Email $email;
    private HashedPassword $password;
    private Roles $roles;
    private \DateTime $createdAt;
    private $sessions;
    private Sessions $_sessions;
    private ?PasswordResetToken $passwordResetToken;

    public function __construct(UserId $userId, Username $username, Email $email, HashedPassword $password, ?Roles $roles)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->id = (string)$userId;
        $this->_id = $userId;
        $this->roles = $roles ?? new Roles();
        $this->createdAt = new \DateTime();
        $this->passwordResetToken = null;

        $this->record(new UserRegistered(
            $userId,
        ));
    }

    public function changePassword(PlainPassword $password, UserPasswordEncoder $encoder, JsonWebToken $currentRefreshToken = null)
    {
        $this->password = $encoder->encodePassword($password);
        $this->passwordResetToken = null;

        if ($currentRefreshToken) {
            $session = $this->getSessions()->findByRefreshToken($currentRefreshToken);
            $this->record(new UserPasswordChanged(
                $this->id,
                $session ? $session->getId() : null
            ));
        } else {
            $this->record(new UserPasswordChanged($this->id));
        }
    }

    public function promote(UserRole $role)
    {
        $this->roles->addRole($role);
        $this->record(new UserPromoted(
            $this->id)
        );
    }

    public function demote(UserRole $role)
    {
        $this->roles->removeRole($role);
        $this->record(new UserDemoted(
            $this->id
        ));
    }

    public function startSession(SessionId $sessionId, ExpiryDate $expiryDate, \DateTime $sessionStart, string $refreshToken)
    {
        $session = new Session(
            $this,
            $sessionId,
            $expiryDate,
            $sessionStart,
            $refreshToken
        );

        $this->sessions[] = $session;
        if (isset($this->_sessions)) {
            $this->_sessions->append($session);
        }
    }

    public function refreshSession(Session $session, JsonWebToken $token)
    {
        $sessions = $this->getSessions();
        $session->refresh($token);

        $sessions->save($session);

        $this->sessions = $sessions->getArrayCopy();
        $this->_sessions = $sessions;
    }

    public function terminateSessions(array $sessionToSkip = [])
    {
        $sessions = $this->getSessions();
        $sessions->terminateSessions($sessionToSkip);

        $this->sessions = $sessions->getArrayCopy();
        $this->_sessions = $sessions;
    }

    public function requestPasswordReset(PasswordResetToken $passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
        $this->record(new UserPasswordResetRequested(
            $this->id,
            $this->email->getValue(),
            $passwordResetToken->getId()
        ));
    }

    public function invalidateSession(Session $session)
    {
        $session->invalidate();
        $this->_sessions->save($session);
    }

    public function getId(): UserId
    {
        if (!isset($this->_id)) {
            $this->_id = UserId::fromString($this->id);
        }

        return $this->_id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function getRoles(): Roles
    {
        return $this->roles;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getSessions(): Sessions
    {
        if (!isset($this->_sessions)) {
            $this->_sessions = new Sessions(iterator_to_array($this->sessions));
        }

        return $this->_sessions;
    }

    public function getPasswordResetToken(): ?PasswordResetToken
    {
        return $this->passwordResetToken;
    }
}