<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\UpdateProjectCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateProjectAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $this->dispatch(new UpdateProjectCommand(
            $subject->getUsername(),
            $request->get('_id'),
            $paramFetcher->getDataParameter('project_description')
        ));

        return $this->respondWithOk();
    }
}