<?php


namespace Overseer\Integration\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

class UpdateWebhookCommand implements Command
{
    private string $issuedBy;
    private string $id;
    private string $url;
    private array $filters;

    public function __construct(string $issuedBy, string $id, string $url, array $filters)
    {
        $this->issuedBy = $issuedBy;
        $this->id = $id;
        $this->url = $url;
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}