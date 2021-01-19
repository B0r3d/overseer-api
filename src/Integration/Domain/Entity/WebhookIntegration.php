<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Integration\Domain\ValueObject\Filters;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Url;
use Overseer\Shared\Domain\ValueObject\Uuid;

class WebhookIntegration extends Integration
{
    private Url $url;
    private Filters $filters;

    public function __construct(IntegrationId $id, Uuid $projectId, Url $url, Filters $filters)
    {
        $this->url = $url;
        $this->filters = $filters;

        parent::__construct($id, $projectId);
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function setUrl(Url $url): void
    {
        $this->url = $url;
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