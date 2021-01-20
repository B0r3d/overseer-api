<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\TelegramIntegration;

class TelegramIntegrationListResource implements \JsonSerializable
{
    private string $id;
    private string $projectId;
    private string $botId;
    private string $chatId;

    public function __construct(TelegramIntegration $integration)
    {
        $this->id = $integration->getId()->value();
        $this->projectId = $integration->getProjectId()->value();
        $this->botId = $integration->getBotId();
        $this->chatId = $integration->getChatId();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'bot_id' => $this->botId,
            'chat_id' => $this->chatId
        ];
    }
}