<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\DeleteWebhookIntegrationCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteWebhookIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $this->dispatch(new DeleteWebhookIntegrationCommand(
            $subject->getUsername(),
            $request->get('_id')
        ));

        return $this->respondWithAccepted();
    }
}