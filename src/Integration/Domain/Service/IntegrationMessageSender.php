<?php


namespace Overseer\Integration\Domain\Service;


use Overseer\Integration\Domain\Entity\Integration;
use Overseer\Integration\Domain\Entity\IntegrationMessage;
use Overseer\Project\Domain\Entity\Error;

interface IntegrationMessageSender
{
    public function sendError(Integration $integration, Error $error): void;
    public function resendMessage(IntegrationMessage $message): void;
}