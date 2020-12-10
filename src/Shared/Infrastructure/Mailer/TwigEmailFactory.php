<?php


namespace Overseer\Shared\Infrastructure\Mailer;


use Overseer\Shared\Domain\Service\EmailFactory;
use Overseer\Shared\Domain\ValueObject\Email;
use Overseer\Shared\Domain\ValueObject\EmailMessage;
use Twig\Environment;

class TwigEmailFactory implements EmailFactory
{
    private string $from;
    private Environment $templateEngine;

    public function __construct(string $from, Environment $templateEngine)
    {
        $this->from = $from;
        $this->templateEngine = $templateEngine;
    }

    public function createEmail(Email $to, string $subject, string $message = '', array $options = []): EmailMessage
    {
        if (!isset($options['template'])) {
            $options['template'] = 'email/default.html.twig';
        }

        if (!isset($options['tokens'])) {
            $options['tokens'] = [];
        }

        $options['tokens']['plain_message'] = $message;
        $options['tokens']['subject'] = $subject;

        $message = $this->templateEngine->render($options['template'], $options['tokens']);
        $from = new Email($this->from);

        return new EmailMessage(
            $from,
            $to,
            $subject,
            $message
        );
    }
}