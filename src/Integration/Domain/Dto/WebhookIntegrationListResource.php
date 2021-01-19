<?php


namespace Overseer\Integration\Domain\Dto;


use Overseer\Integration\Domain\Entity\WebhookIntegration;

class WebhookIntegrationListResource implements \JsonSerializable
{
    private string $id;
    private string $projectId;
    private string $url;

    public function __construct(WebhookIntegration $integration)
    {
        $this->id = $integration->getId()->value();
        $this->projectId = $integration->getProjectId();
        $this->url = $integration->getUrl();
    }


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'url' => $this->url
        ];
    }
}