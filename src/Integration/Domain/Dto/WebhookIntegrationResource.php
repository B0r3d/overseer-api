<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\WebhookIntegration;

class WebhookIntegrationResource implements \JsonSerializable
{
    private string $id;
    private string $projectId;
    private string $url;
    private array $filters;
    private string $createdAt;

    public function __construct(WebhookIntegration $integration)
    {
        $this->id = $integration->getId()->value();
        $this->projectId = $integration->getProjectId();
        $this->url = $integration->getUrl();
        $this->filters = $integration->getFilters()->getFilters();
        $this->createdAt = $integration->getCreatedAt()->format(\DateTime::ISO8601);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'url' => $this->url,
            'filters' => $this->filters,
            'created_at' => $this->createdAt,
        ];
    }
}