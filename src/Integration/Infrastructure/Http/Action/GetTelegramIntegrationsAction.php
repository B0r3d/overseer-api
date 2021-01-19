<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\TelegramIntegrationListResource;
use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationsQuery;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetTelegramIntegrationsAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetTelegramIntegrationsQuery(
            $subject->getUsername(),
            $paramFetcher->getQueryParameter('project_id', ''),
            $paramFetcher->getQueryParameter('page', 1)
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $resources = [];

        /** @var TelegramIntegration $item */
        foreach ($result->getItems() as $item) {
            $resources[] = new TelegramIntegrationListResource($item);
        }

        return $this->respondWithOk(new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        ));
    }
}