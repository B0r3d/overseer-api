<?php


namespace Overseer\Integration\Domain\Service;


use Overseer\Integration\Domain\Entity\WebhookIntegration;

interface WebhookIntegrationWriteModel
{
    public function save(WebhookIntegration $integration): void;
    public function delete(WebhookIntegration $integration): void;
}