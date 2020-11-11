<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Project\Application\Command\InviteMember\InviteMember;
use Overseer\Project\Domain\Dto\InviteNewMemberRequest;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InviteNewMember extends AbstractController
{
    private SerializerInterface $serializer;
    private CommandBus $commandBus;

    public function __construct(SerializerInterface $serializer, CommandBus $commandBus)
    {
        $this->serializer = $serializer;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request, $_project_uuid): Response
    {
        $user = $this->getUser();

        /** @var InviteNewMemberRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), InviteNewMemberRequest::class, 'json');

        if (!$dto->isValid() || !Uuid::isValid($_project_uuid)) {
            throw new BadRequestHttpException();
        }

        $command = new InviteMember(
            $dto->invitationId(),
            $user->getUsername(),
            $_project_uuid,
            $dto->username(),
            $dto->email(),
        );

        $this->commandBus->dispatch($command);

        return $this->json([
            'ok' => true,
            'payload' => [
                'invitation' => [
                    'uuid' => $command->invitationId(),
                    'project_id' => $command->projectId(),
                    'username' => $command->username(),
                    'email' => $command->email(),
                ]
            ],
        ], Response::HTTP_CREATED);
    }
}