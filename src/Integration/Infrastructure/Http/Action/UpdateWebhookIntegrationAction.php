<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\UpdateWebhookCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateWebhookIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $command = new UpdateWebhookCommand(
            $subject->getUsername(),
            $request->get('_id'),
            $paramFetcher->getDataParameter('url', ''),
            $paramFetcher->getDataParameter('filters', [])
        );

        $this->dispatch($command);

        return $this->respondWithOk();
    }
}