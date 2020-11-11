<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Application\Command\RemoveInvitation\RemoveInvitation;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CancelInvitation extends AbstractController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request, $_invitation_uuid): Response
    {
        $user = $this->getUser();

        if (!Uuid::isValid($_invitation_uuid)) {
            throw new BadRequestHttpException();
        }

        $command = new RemoveInvitation(
            $_invitation_uuid,
            $user->getUsername(),
        );

        $this->commandBus->dispatch($command);
        $response = [
            'ok' => true,
        ];

        return $this->json($response);
    }
}