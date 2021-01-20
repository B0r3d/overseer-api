<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\WebhookIntegrationMessageResource;
use Overseer\Integration\Domain\Entity\WebhookIntegrationMessage;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationMessagesQuery;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetWebhookIntegrationMessagesAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetWebhookIntegrationMessagesQuery(
            $subject->getUsername(),
            $request->get('_id'),
            $paramFetcher->getQueryParameter('page', 1)
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $resources = [];

        /** @var WebhookIntegrationMessage $item */
        foreach ($result->getItems() as $item) {
            $resources[] = new WebhookIntegrationMessageResource($item);
        }

        return $this->respondWithOk(new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        ));
    }
}