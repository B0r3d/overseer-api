<?php


namespace Overseer\Integration\Infrastructure\Http\Action;


use Overseer\Integration\Domain\Command\DeleteTelegramIntegrationCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTelegramIntegrationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $this->dispatch(new DeleteTelegramIntegrationCommand(
            $subject->getUsername(),
            $request->get('_id')
        ));

        return $this->respondWithAccepted();
    }
}