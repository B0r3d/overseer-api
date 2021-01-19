<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Dto\TelegramIntegrationResource;
use Overseer\Integration\Domain\Query\GetTelegramIntegrationQuery;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetTelegramIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $query = new GetTelegramIntegrationQuery(
            $subject->getUsername(),
            $request->get('_id')
        );

        /** @var SingleObjectResult $result */
        $result = $this->ask($query);

        return $this->respondWithOk(new TelegramIntegrationResource($result->getData()));
    }
}