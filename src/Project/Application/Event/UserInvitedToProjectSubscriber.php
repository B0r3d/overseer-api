<?php


namespace Overseer\Project\Application\Event;


use Overseer\Project\Domain\Event\UserInvitedToProject;
use Overseer\Shared\Domain\Bus\Event\EventSubscriber;
use Overseer\Shared\Domain\Service\EmailFactory;
use Overseer\Shared\Domain\Service\Mailer;
use Overseer\Shared\Domain\ValueObject\Email;

final class UserInvitedToProjectSubscriber implements EventSubscriber
{
    private EmailFactory $emailFactory;
    private Mailer $mailer;
    private string $acceptInvitationPage;

    public function __construct(EmailFactory $emailFactory, Mailer $mailer, $acceptInvitationPage)
    {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->acceptInvitationPage = $acceptInvitationPage;
    }

    static function getSubscribedDomainEvents(): array
    {
        return [
            UserInvitedToProject::class
        ];
    }

    public function __invoke(UserInvitedToProject $event)
    {
        $email = $this->emailFactory->createEmail(
            new Email($event->getUserEmail()),
            'Project invitation',
            '',
            [
                'template' => 'email/project-invitation.html.twig',
                'tokens' => [
                    'url' => $this->acceptInvitationPage . $event->getInvitationId() . '&projectId=' . $event->aggregateId()
                ]
            ]
        );

        $this->mailer->sendEmail($email);
    }
}