<?php


namespace Overseer\Shared\Domain\Service;


use Overseer\Shared\Domain\ValueObject\Email;
use Overseer\Shared\Domain\ValueObject\EmailMessage;

interface EmailFactory
{
    public function createEmail(Email $to, string $subject, string $message = '', array $options = []): EmailMessage;
}