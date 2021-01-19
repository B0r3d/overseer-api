<?php


namespace Overseer\Integration\Application;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Overseer\Integration\Domain\Entity\Integration;
use Overseer\Integration\Domain\Entity\IntegrationMessage;
use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Entity\TelegramIntegrationMessage;
use Overseer\Integration\Domain\Service\IntegrationMessageSender;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Shared\Domain\ValueObject\Uuid;

class TelegramMessageSender implements IntegrationMessageSender
{
    public function sendError(Integration $integration, Error $error): void
    {
        if (!$integration instanceof TelegramIntegration) {
            throw new \InvalidArgumentException('Invalid integration provided');
        }

        $telegramMessage = sprintf("Exception <strong>\"%s\"</strong> occurred at <strong>%s</strong>.\nError code: <strong>%s</strong>\nError message: <strong>%s</strong>\nFile: <strong>%s</strong>\nLine: <strong>%d</strong>.",
            $error->getException()->getClass(),
            $error->getOccurredAt()->format('d.m.Y, H:i'),
            $error->getException()->getErrorCode(),
            $error->getException()->getErrorMessage(),
            $error->getException()->getFile(),
            $error->getException()->getLine()
        );

        $message = new TelegramIntegrationMessage(
            $integration,
            $error->getId(),
            Uuid::random(),
            $telegramMessage
        );

        try {
            $client = new Client();
            $url = sprintf("https://api.telegram.org/bot%s/sendMessage?chat_id=%s&text=%s&parse_mode=HTML",
                $integration->getBotId(),
                $integration->getChatId(),
                urlencode($message->getTelegramMessage())
            );

            $client->get($url);
            $message->markAsProcessed();
        } catch(RequestException $exception) {
            $response = $exception->getResponse()->getBody()->getContents();
            $json = json_decode($response, true);
            $message->markAsFailed($json['description']);
        }

        $integration->addMessage($message);
    }

    public function resendMessage(IntegrationMessage $message): void
    {
        if (!$message instanceof TelegramIntegrationMessage) {
            throw new \InvalidArgumentException('Unsupported message this class requires to provide object of ' . TelegramIntegrationMessage::class);
        }

        $message->increaseAttemptCount();

        /** @var TelegramIntegration $integration */
        $integration = $message->getIntegration();

        try {
            $client = new Client();
            $url = sprintf("https://api.telegram.org/bot%s/sendMessage?chat_id=%s&text=%s&parse_mode=HTML",
                $integration->getBotId(),
                $integration->getChatId(),
                urlencode($message->getTelegramMessage())
            );

            $client->get($url);
            $message->markAsProcessed();
        } catch(RequestException $exception) {
            $response = $exception->getResponse()->getBody()->getContents();
            $json = json_decode($response, true);
            $message->markAsFailed($json['description']);
        }
    }
}