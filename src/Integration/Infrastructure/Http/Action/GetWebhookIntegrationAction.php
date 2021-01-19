<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\WebhookIntegrationResource;
use Overseer\Integration\Domain\Query\GetWebhookIntegrationQuery;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetWebhookIntegrationAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $query = new GetWebhookIntegrationQuery(
            $request->get('_id'),
            $subject->getUsername()
        );

        /** @var SingleObjectResult $result */
        $result = $this->ask($query);

        $resource = new WebhookIntegrationResource($result->getData());

        return $this->respondWithOk($resource);
    }
}