<?php


namespace Overseer\Integration\Domain\Validator;


use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\Validator\Specification;

class ValidTelegramIntegration implements Specification
{
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;

    public function __construct(TelegramIntegrationReadModel $telegramIntegrationReadModel)
    {
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($value));
        return $integration !== null;
    }
}