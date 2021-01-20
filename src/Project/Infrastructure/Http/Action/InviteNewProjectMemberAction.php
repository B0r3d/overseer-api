<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\InviteMemberCommand;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class InviteNewProjectMemberAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $invitationId = ProjectMemberInvitationId::random();
        $command = new InviteMemberCommand(
            $invitationId->value(),
            $subject->getUsername(),
            $request->get('_project_id'),
            $paramFetcher->getDataParameter('username'),
            $paramFetcher->getDataParameter('email'),
        );

        $this->dispatch($command);

        return $this->respondWithCreated([
            'invitation' => [
                'id' => $command->getInvitationId(),
                'project_id' => $command->getProjectId(),
                'username' => $command->getUsername(),
                'email' => $command->getEmail(),
                'invited_by' => [
                    'username' => $subject->getUsername()
                ]
            ]
        ]);
    }
}