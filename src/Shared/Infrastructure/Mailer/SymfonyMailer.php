<?php


namespace Overseer\Shared\Infrastructure\Mailer;


use Overseer\Shared\Domain\Service\Mailer;
use Overseer\Shared\Domain\ValueObject\EmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SymfonyMailer implements Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(EmailMessage $emailMessage): void
    {
        $email = new Email();
        $email->subject($emailMessage->getSubject())
            ->to($emailMessage->getTo()->getValue())
            ->from($emailMessage->getFrom()->getValue())
            ->html($emailMessage->getMessage())
        ;

        $this->mailer->send($email);
    }
}