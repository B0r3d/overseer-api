<?php


namespace Overseer\Integration\Application;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Overseer\Integration\Domain\Entity\Integration;
use Overseer\Integration\Domain\Entity\IntegrationMessage;
use Overseer\Integration\Domain\Entity\WebhookIntegrationMessage;
use Overseer\Integration\Domain\Service\IntegrationMessageSender;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\ValueObject\Exception;
use Overseer\Shared\Domain\ValueObject\Uuid;

class WebhookMessageSender implements IntegrationMessageSender
{
    public function sendError(Integration $webhookIntegration, Error $error): void
    {
        $message = new WebhookIntegrationMessage(
            $webhookIntegration,
            $error->getId(),
            Uuid::random(),
            $this->createJson($error)
        );

        try {
            $client = new Client();
            $headers = [
                'content-type' => 'application/json'
            ];

            $request = new Request(
                'POST',
                $webhookIntegration->getUrl(),
                $headers,
                json_encode($message->getJson())
            );

            $response = $client->send($request);
            if ($response->getStatusCode() === 200) {
                $message->markAsProcessed();
                $message->setStatusCode(200);
            } else {
                $message->markAsFailed('Response status code is different than 200');
                $message->scheduleNextAttempt();
                $message->setStatusCode($response->getStatusCode());
            }
        } catch(ClientException $exception) {
            $message->markAsFailed($exception->getResponse()->getBody()->getContents());
            $message->scheduleNextAttempt();
            $message->setStatusCode($exception->getResponse()->getStatusCode());
        } catch(ConnectException $exception) {
            $message->markAsFailed('Failed to connect to ' . $exception->getRequest()->getUri()->getHost() . $exception->getRequest()->getUri()->getPath());
            $message->scheduleNextAttempt();
            $message->setStatusCode(404);
        }

        $webhookIntegration->addMessage($message);
    }

    protected function createJson(Error $error)
    {
        $json = [
            'project_id' => $error->getProject()->getId()->value(),
            'exception' => [
                'class' => $error->getException()->getClass(),
                'error_code' => $error->getException()->getErrorCode(),
                'error_message' => $error->getException()->getErrorMessage(),
                'line' => $error->getException()->getLine(),
                'file' => $error->getException()->getFile(),
                'stacktrace' => []
            ]
        ];

        /** @var Exception $exception */
        foreach ($error->getStacktrace() as $exception) {
            $json['exception']['stacktrace'] = [
                'class' => $exception->getClass(),
                'error_code' => $exception->getErrorCode(),
                'error_message' => $exception->getErrorMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
            ];
        }

        return $json;
    }

    public function resendMessage(IntegrationMessage $message): void
    {
        if (!$message instanceof WebhookIntegrationMessage) {
            throw new \InvalidArgumentException('Unsupported message this class requires to provide object of ' . WebhookIntegrationMessage::class);
        }

        $message->increaseAttemptCount();

        try {
            $client = new Client();
            $headers = [
                'content-type' => 'application/json'
            ];

            $request = new Request(
                'POST',
                $message->getIntegration()->getUrl(),
                $headers,
                json_encode($message->getJson())
            );

            $response = $client->send($request);
            if ($response->getStatusCode() === 200) {
                $message->markAsProcessed();
                $message->setStatusCode(200);
            } else {
                $message->markAsFailed('Response status code is different than 200');
                $message->scheduleNextAttempt();
                $message->setStatusCode($response->getStatusCode());
            }
        } catch(ClientException $exception) {
            $message->markAsFailed($exception->getResponse()->getBody()->getContents());
            $message->scheduleNextAttempt();
            $message->setStatusCode($exception->getResponse()->getStatusCode());
        } catch(ConnectException $exception) {
            $message->markAsFailed('Failed to connect to ' . $exception->getRequest()->getUri()->getHost() . $exception->getRequest()->getUri()->getPath());
            $message->scheduleNextAttempt();
            $message->setStatusCode(404);
        }
    }
}