<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Shared\Domain\ValueObject\Uuid;

class TelegramIntegrationMessage extends IntegrationMessage
{
    private string $telegramMessage;

    public function __construct(Integration $integration, Uuid $errorId, Uuid $id, string $telegramMessage)
    {
        $this->telegramMessage = $telegramMessage;

        parent::__construct($integration, $errorId, $id);
    }

    public function getTelegramMessage(): string
    {
        return $this->telegramMessage;
    }
}