<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\TelegramIntegrationMessageResource;
use Overseer\Integration\Domain\Entity\TelegramIntegrationMessage;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationMessagesQuery;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetTelegramIntegrationMessagesAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetTelegramIntegrationMessagesQuery(
            $subject->getUsername(),
            $request->get('_id'),
            $paramFetcher->getQueryParameter('page', 1)
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $resources = [];

        /** @var TelegramIntegrationMessage $item */
        foreach ($result->getItems() as $item) {
            $resources[] = new TelegramIntegrationMessageResource($item);
        }

        return $this->respondWithOk(new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        ));
    }
}