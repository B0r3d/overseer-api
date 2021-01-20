<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\DeleteProjectCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteProjectAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $this->dispatch(new DeleteProjectCommand(
            $subject->getUsername(),
            $request->get('_id')
        ));

        return $this->respondWithAccepted();
    }
}