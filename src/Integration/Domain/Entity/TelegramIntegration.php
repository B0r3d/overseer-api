<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

class TelegramIntegration extends Integration
{
    private string $botId;
    private string $chatId;
    private Filters $filters;

    public function __construct(IntegrationId $id, Uuid $projectId, string $botId, string $chatId, Filters $filters)
    {
        $this->botId = $botId;
        $this->chatId = $chatId;
        $this->filters = $filters;

        parent::__construct($id, $projectId);
    }

    public function getBotId(): string
    {
        return $this->botId;
    }

    public function setBotId(string $botId): void
    {
        $this->botId = $botId;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getFilters(): Filters
    {
        return $this->filters;
    }

    public function setFilters(Filters $filters): void
    {
        $this->filters = $filters;
    }
}