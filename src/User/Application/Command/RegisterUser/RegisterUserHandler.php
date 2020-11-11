<?php


namespace Overseer\User\Application\Command\RegisterUser;


use Overseer\Shared\Domain\Bus\Command\CommandHandler;
use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Exception\UserAlreadyExistsException;
use Overseer\User\Domain\Service\UserPasswordEncoder;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\Email;
use Overseer\User\Domain\ValueObject\Password;
use Overseer\User\Domain\ValueObject\UserId;
use Overseer\User\Domain\ValueObject\Username;

final class RegisterUserHandler implements CommandHandler
{
    private UserWriteModel $userWriteModel;
    private UserReadModel $userReadModel;
    private UserPasswordEncoder $userPasswordEncoder;
    private EventBus $eventBus;

    public function __construct(UserWriteModel $userWriteModel, UserReadModel $userReadModel, UserPasswordEncoder $userPasswordEncoder, EventBus $eventBus)
    {
        $this->userWriteModel = $userWriteModel;
        $this->userReadModel = $userReadModel;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->eventBus = $eventBus;
    }


    public function __invoke(RegisterUser $command): void
    {
//        $user = $this->userFactory->createNewUser(
//            $command->username(),
//            $command->email(),
//            $command->uuid(),
//            $command->password(),
//        );
//
//        $domainEvents = $user->pullDomainEvents();
//        $this->userWriteModel->save($user);
//        $this->eventBus->publish(...$domainEvents);


        $username = new Username($command->username());
        $email = new Email($command->email());

        $dbUser = $this->userReadModel->findOneByUsernameOrEmail($username, $email);

        if($dbUser) {
            throw new UserAlreadyExistsException();
        }

        $uuid = UserId::fromString($command->uuid());
        $password = new Password($command->password());

        $user = User::create(
            $uuid,
            $username,
            $email
        );

        $user->setNewPassword($password, $this->userPasswordEncoder, false);
        $this->userWriteModel->save($user);

        $domainEvents = $user->pullDomainEvents();
        $this->eventBus->publish(...$domainEvents);
    }
}