<?php


namespace Overseer\Integration\Domain\Service;


use Overseer\Integration\Domain\Entity\TelegramIntegration;

interface TelegramIntegrationWriteModel
{
    public function save(TelegramIntegration $integration): void;
    public function delete(TelegramIntegration $integration): void;
}