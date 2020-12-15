<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\CreateApiKeyCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateApiKeyAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $this->dispatch(new CreateApiKeyCommand(
            $subject->getUsername(),
            $request->get('_project_id'),
            $paramFetcher->getDataParameter('expiry_date', null)
        ));

        return $this->respondWithCreated();
    }
}