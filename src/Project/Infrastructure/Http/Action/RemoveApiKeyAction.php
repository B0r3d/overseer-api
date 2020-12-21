<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\RemoveApiKeyCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RemoveApiKeyAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $this->dispatch(new RemoveApiKeyCommand(
            $subject->getUsername(),
            $request->get('_api_key_id'),
            $request->get('_project_id')
        ));

        return $this->respondWithOk();
    }
}