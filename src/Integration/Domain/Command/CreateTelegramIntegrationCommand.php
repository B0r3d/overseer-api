<?php


namespace Overseer\Integration\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class CreateTelegramIntegrationCommand implements Command
{
    private string $issuedBy;
    private string $id;
    private string $projectId;
    private string $botId;
    private string $chatId;
    private array $filters;

    public function __construct(string $issuedBy, string $id, string $projectId, string $botId, string $chatId, array $filters)
    {
        $this->issuedBy = $issuedBy;
        $this->id = $id;
        $this->projectId = $projectId;
        $this->botId = $botId;
        $this->chatId = $chatId;
        $this->filters = $filters;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getBotId(): string
    {
        return $this->botId;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}