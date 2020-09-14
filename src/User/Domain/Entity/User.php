<?php


namespace Overseer\User\Domain\Entity;


use Overseer\Shared\Domain\Aggregate\AggregateRoot;
use Overseer\User\Domain\Event\UserDemoted;
use Overseer\User\Domain\Event\UserPasswordChanged;
use Overseer\User\Domain\Event\UserPromoted;
use Overseer\User\Domain\Event\UserRegistered;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\Password;
use Overseer\User\Domain\ValueObject\RefreshTokenId;
use Overseer\User\Domain\ValueObject\Roles;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

class User extends AggregateRoot
{
    private ?int $id;
    private UserId $uuid;
    private Username $username;
    private Email $email;
    private Password $password;
    private iterable $refreshTokens;
    private Roles $roles;
    private \DateTime $createdAt;

    private function __construct(UserId $userId, Username $username, Email $email, ?Roles $roles, ?\DateTime $createdAt)
    {
        $this->id = null;
        $this->username = $username;
        $this->email = $email;
        $this->uuid = $userId;
        $this->refreshTokens = [];
        $this->roles = $roles ?? new Roles();
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    public static function create(UserId $userId, Username $username, Email $email, ?Roles $roles = null, \DateTime $createdAt = null): self
    {
        $instance = new self($userId, $username, $email, $roles, $createdAt);
        $instance->record(new UserRegistered(
            $instance->uuid,
        ));

        return $instance;
    }

    public function setNewPassword(string $password, UserPasswordEncoder $encoder, bool $publishEvent = true)
    {
        $this->password = $encoder->encodePassword($password);

        if ($publishEvent) {
            $this->record(new UserPasswordChanged(
                $this->uuid->value()
            ));
        }
    }

    public function promote(string $role)
    {
        $this->roles->addRole($role);
        $this->record(new UserPromoted($this->uuid));
    }

    public function demote(string $role)
    {
        $this->roles->removeRole($role);
        $this->record(new UserDemoted($this->uuid));
    }

    public function addRefreshToken(RefreshToken $refreshToken): void
    {
        $this->refreshTokens[] = $refreshToken;
    }

    public function removeRefreshToken(RefreshTokenId $refreshTokenId)
    {
        $index = -1;

        /** @var RefreshToken $refreshToken */
        foreach($this->refreshTokens as $loopIndex => $refreshToken) {
            if($refreshToken->uuid()->equals($refreshTokenId)) {
                $index = $loopIndex;
                break;
            }
        }

        if ($index === -1) {
            return;
        }

        unset($this->refreshTokens[$index]);
    }

    public function isValid(RefreshTokenId $refreshTokenId): bool
    {
        $matchingToken = null;

        /** @var RefreshToken $refreshToken */
        foreach($this->refreshTokens as $refreshToken) {
            if($refreshToken->uuid()->equals($refreshTokenId)) {
                $matchingToken = $refreshToken;
                break;
            }
        }

        if (!$matchingToken) {
            return false;
        }

        return $refreshToken->isValid() && !$refreshToken->isExpired();
    }

    public function uuid(): UserId
    {
        return $this->uuid;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function roles(): Roles
    {
        return $this->roles;
    }

    public function jwtPayload()
    {
        return [
            'uuid' => $this->uuid->value(),
            'roles' => $this->roles->toArray(),
        ];
    }
}