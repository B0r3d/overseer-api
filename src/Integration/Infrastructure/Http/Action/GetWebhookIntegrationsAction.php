<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\WebhookIntegrationListResource;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationsQuery;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetWebhookIntegrationsAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetWebhookIntegrationsQuery(
            $subject->getUsername(),
            $paramFetcher->getQueryParameter('project_id', ''),
            $paramFetcher->getQueryParameter('page', 1)
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $resources = [];

        /** @var WebhookIntegration $item */
        foreach ($result->getItems() as $item) {
            $resources[] = new WebhookIntegrationListResource($item);
        }

        return $this->respondWithOk(new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        ));
    }
}