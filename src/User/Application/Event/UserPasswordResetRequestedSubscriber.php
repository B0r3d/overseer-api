<?php


namespace Overseer\User\Application\Event;


use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\Shared\Domain\Service\EmailFactory;
use Overseer\Shared\Domain\Service\Mailer;
use Overseer\Shared\Domain\ValueObject\Email;
use Overseer\User\Domain\Event\UserPasswordResetRequested;

class UserPasswordResetRequestedSubscriber implements EventSubscriber
{
    private Mailer $mailer;
    private EmailFactory $emailFactory;
    private string $newPasswordPage;

    public function __construct(Mailer $mailer, EmailFactory $emailFactory, string $newPasswordPage)
    {
        $this->mailer = $mailer;
        $this->emailFactory = $emailFactory;
        $this->newPasswordPage = $newPasswordPage;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            UserPasswordResetRequested::class,
        ];
    }

    public function __invoke(UserPasswordResetRequested $event)
    {
        $email = $this->emailFactory->createEmail(
            new Email($event->getEmail()),
            'Resetting password',
            '',
            [
                'template' => 'email/reset-password-email.html.twig',
                'tokens' => [
                    'url' => $this->newPasswordPage . $event->getPasswordResetTokenId()
                ]
            ]
        );

        $this->mailer->sendEmail($email);
    }
}