<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\CreateWebhookIntegrationCommand;
use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateWebhookIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $paramFetcher = $this->getParamFetcher($request);

        $command = new CreateWebhookIntegrationCommand(
            $subject->getUsername(),
            Uuid::random(),
            $paramFetcher->getDataParameter('project_id', ''),
            $paramFetcher->getDataParameter('url', ''),
            $paramFetcher->getDataParameter('filters', [])
        );

        $this->dispatch($command);

        return $this->respondWithCreated([
            'id' => $command->getId(),
            'project_id' => $command->getProjectId(),
            'url' => $command->getUrl(),
            'filters' => $command->getFilters()
        ]);
    }
}