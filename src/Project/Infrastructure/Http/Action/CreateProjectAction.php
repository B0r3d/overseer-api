<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\CreateProjectCommand;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateProjectAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $projectId = ProjectId::random();

        $command = new CreateProjectCommand(
            $projectId->value(),
            $paramFetcher->getDataParameter('project_title', ''),
            $subject->getUsername(),
            $paramFetcher->getDataParameter('project_description', '')
        );

        $this->dispatch($command);

        return $this->respondWithCreated([
            'project' => [
                'id' => $command->getProjectId(),
                'title' => $command->getProjectTitle(),
                'description' => $command->getDescription(),
                'project_owner' => [
                    'username' => $command->getProjectOwner(),
                ]
            ]
        ]);
    }
}