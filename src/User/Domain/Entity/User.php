<?php


namespace Overseer\User\Domain\Entity;


use Overseer\Shared\Domain\Aggregate\AggregateRoot;
use Overseer\User\Domain\Event\UserPasswordChanged;
use Overseer\User\Domain\Event\UserRegistered;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\Password;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

class User extends AggregateRoot
{
    private ?int $id;
    private UserId $uuid;
    private Username $username;
    private Email $email;
    private Password $password;

    private function __construct(UserId $userId, Username $username, Email $email)
    {
        $this->id = null;
        $this->username = $username;
        $this->email = $email;
        $this->uuid = $userId;
    }

    public static function create(UserId $userId, Username $username, Email $email): self
    {
        $instance = new self($userId, $username, $email);
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
}