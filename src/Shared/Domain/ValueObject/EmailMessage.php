<?php


namespace Overseer\Shared\Domain\ValueObject;


class EmailMessage
{
    private Email $from;
    private Email $to;
    private string $subject;
    private string $message;

    public function __construct(Email $from, Email $to, string $subject, string $message)
    {
        if (mb_strlen($subject) === 0) {
            throw new \InvalidArgumentException('Subject cannot be empty');
        }

        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function getFrom(): Email
    {
        return $this->from;
    }

    public function getTo(): Email
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}