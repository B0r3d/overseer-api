<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Command\CreateErrorCommand;
use Overseer\Project\Domain\Service\ProjectResolver;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateErrorAction extends AbstractAction
{
    private ProjectResolver $projectResolver;

    public function __construct(ProjectResolver $projectResolver)
    {
        $this->projectResolver = $projectResolver;
    }

    public function __invoke(Request $request): Response
    {
        $apiKey = $request->headers->get('X-API-KEY', '');

        $project = $this->projectResolver->resolve($apiKey);

        if (!$project) {
            throw new ValidationException('Invalid API key');
        }

        $paramFetcher = $this->getParamFetcher($request);

        $command = new CreateErrorCommand(
            $project->getId()->value(),
            Uuid::random()->value(),
            $paramFetcher->getDataParameter('exception_class', ''),
            $paramFetcher->getDataParameter('error_code', '0'),
            $paramFetcher->getDataParameter('error_message', ''),
            $paramFetcher->getDataParameter('line', 0),
            $paramFetcher->getDataParameter('file', ''),
            $paramFetcher->getDataParameter('occurred_at', ''),
            $paramFetcher->getDataParameter('stacktrace', [])
        );

        $this->dispatch($command);

        return $this->respondWithOk([
            'project_id' => $command->getProjectId(),
            'id' => $command->getErrorId(),
            'exception_class' => $command->getClass(),
            'error_code' => $command->getErrorCode(),
            'error_message' => $command->getErrorMessage(),
            'line' => $command->getLine(),
            'file' => $command->getFile(),
            'occurred_at' => $command->getOccurredAt(),
            'stacktrace' => $command->getStacktrace()
        ]);
    }
}