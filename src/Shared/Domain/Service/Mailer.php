<?php


namespace Overseer\Shared\Domain\Service;


use Overseer\Shared\Domain\ValueObject\EmailMessage;

interface Mailer
{
    public function sendEmail(EmailMessage $email): void;
}