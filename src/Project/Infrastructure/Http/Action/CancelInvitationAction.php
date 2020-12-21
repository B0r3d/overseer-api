<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\CancelInvitationCommand;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CancelInvitationAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $command = new CancelInvitationCommand(
            $request->get('_invitation_id'),
            $subject->getUsername(),
            $request->get('_project_id')
        );

        $this->dispatch($command);

        return $this->respondWithOk();
    }
}