<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Project\Domain\Dto\CreateProjectRequest;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Overseer\Project\Application\Command\CreateProject\CreateProject as CreateProjectCommand;

final class CreateProject extends AbstractController
{
    private SerializerInterface $serializer;
    private CommandBus $commandBus;

    public function __construct(SerializerInterface $serializer, CommandBus $commandBus)
    {
        $this->serializer = $serializer;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();

        /** @var CreateProjectRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateProjectRequest::class, 'json');

        if (!$dto->isValid()) {
            throw new BadRequestHttpException();
        }

        $command = new CreateProjectCommand(
            $dto->uuid(),
            $dto->projectTitle(),
            $dto->projectSlug(),
            $user->getUsername(),
            $dto->description()
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse([
            'ok' => true,
            'payload' => [
                'project' => [
                    'uuid' => $command->projectId(),
                    'project_title' => $command->projectTitle(),
                    'project_slug' => $command->projectSlug(),
                    'description' => $command->description(),
                    'project_owner' => $command->projectOwner(),
                ]
            ]
        ], Response::HTTP_CREATED);
    }
}