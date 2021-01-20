<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\UpdateTelegramIntegrationCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTelegramIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $paramFetcher = $this->getParamFetcher($request);

        $command = new UpdateTelegramIntegrationCommand(
            $subject->getUsername(),
            $request->get('_id'),
            $paramFetcher->getDataParameter('bot_id', ''),
            $paramFetcher->getDataParameter('chat_id', ''),
            $paramFetcher->getDataParameter('filters', [])
        );

        $this->dispatch($command);

        return $this->respondWithOk();
    }
}