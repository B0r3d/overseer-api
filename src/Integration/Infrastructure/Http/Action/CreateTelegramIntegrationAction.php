<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\CreateTelegramIntegrationCommand;
use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateTelegramIntegrationAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $paramFetcher = $this->getParamFetcher($request);

        $command = new CreateTelegramIntegrationCommand(
            $subject->getUsername(),
            Uuid::random(),
            $paramFetcher->getDataParameter('project_id', ''),
            $paramFetcher->getDataParameter('bot_id', ''),
            $paramFetcher->getDataParameter('chat_id', ''),
            $paramFetcher->getDataParameter('filters', [])
        );

        $this->dispatch($command);

        return $this->respondWithCreated([
            'id' => $command->getId(),
            'project_id' => $command->getProjectId(),
            'bot_id' => $command->getBotId(),
            'chat_id' => $command->getChatId(),
            'filters' => $command->getFilters(),
        ]);
    }
}