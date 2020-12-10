<?php


namespace Overseer\Shared\Infrastructure\CLI;


use Overseer\Shared\Domain\Service\EmailFactory;
use Overseer\Shared\Domain\Service\Mailer;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\Email;
use Overseer\Shared\Domain\Validator\Specification\MinLength;
use Overseer\Shared\Domain\Validator\ValidationContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MailerSendEmailCommand extends Command
{
    protected static $defaultName = 'overseer:shared:mailer-send-email';
    private Mailer $mailer;
    private EmailFactory $emailFactory;

    public function __construct(Mailer $mailer, EmailFactory $emailFactory, string $name = null)
    {
        $this->mailer = $mailer;
        $this->emailFactory = $emailFactory;

        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $to = $io->ask('Whom would you like to send the email?');
        $subject = $io->ask('What\'s the subject?');
        $message = $io->ask('What\'s the message?');

        $validationContext = new ValidationContext([
            new Field($to, 'Invalid email provided.', [
                new Email()
            ]),
            new Field($subject, 'Subject cannot be empty', [
                new MinLength(4)
            ])
        ]);

        if (!$validationContext->isValid()) {
            $io->error('Error: ' . $validationContext->getErrorMessage());
            return;
        }

        $to = new \Overseer\Shared\Domain\ValueObject\Email($to);
        $email = $this->emailFactory->createEmail($to, $subject, $message);

        $this->mailer->sendEmail($email);
    }
}