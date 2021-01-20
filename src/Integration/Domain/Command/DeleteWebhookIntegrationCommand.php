<?php


namespace Overseer\Integration\Domain\Command;


use Overseer\Shared\Domain\Bus\Command\Command;

final class DeleteWebhookIntegrationCommand implements Command
{
    private string $issuedBy;
    private string $id;

    public function __construct(string $issuedBy, string $id)
    {
        $this->issuedBy = $issuedBy;
        $this->id = $id;
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    public function getId(): string
    {
        return $this->id;
    }
}