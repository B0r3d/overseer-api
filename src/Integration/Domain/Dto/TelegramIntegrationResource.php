<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\TelegramIntegration;

class TelegramIntegrationResource implements \JsonSerializable
{
    private string $id;
    private string $projectId;
    private string $botId;
    private string $chatId;
    private array $filters;
    private string $createdAt;

    public function __construct(TelegramIntegration $integration)
    {
        $this->id = $integration->getId()->value();
        $this->projectId = $integration->getProjectId()->value();
        $this->botId = $integration->getBotId();
        $this->chatId = $integration->getChatId();
        $this->filters = $integration->getFilters()->getFilters();
        $this->createdAt = $integration->getCreatedAt()->format(\DateTime::ISO8601);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'bot_id' => $this->botId,
            'chat_id' => $this->chatId,
            'created_at' => $this->createdAt,
            'filters' => $this->filters
        ];
    }
}