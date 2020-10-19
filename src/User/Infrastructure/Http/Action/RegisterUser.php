<?php


namespace Overseer\User\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Overseer\User\Domain\Dto\RegisterUserRequest;
use Overseer\User\Domain\Exception\UserAlreadyExistsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Overseer\User\Application\Command\RegisterUser\RegisterUser as RegisterUserCommand;

final class RegisterUser
{
    private CommandBus $commandBus;
    private SerializerInterface $serializer;

    public function __construct(CommandBus $commandBus, SerializerInterface $serializer)
    {
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request): Response
    {
        try {
            /** @var RegisterUserRequest $dto */
            $dto = $this->serializer->deserialize($request->getContent(), RegisterUserRequest::class, 'json');

            if (!$dto->isValid()) {
                throw new BadRequestHttpException();
            }

            $command = new RegisterUserCommand(
                $dto->username(),
                $dto->uuid(),
                $dto->email(),
                $dto->password()
            );

            $this->commandBus->dispatch($command);
            $statusCode = Response::HTTP_CREATED;
            $response = [
               'ok' => true,
               'payload' => [
                   'username' => $dto->username(),
                   'email' => $dto->email(),
                   'uuid' => $dto->uuid(),
               ],
            ];
        } catch(UserAlreadyExistsException $exception) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            $response = [
                'ok' => false,
                'error_message' => 'User already exists'
            ];
        } catch(BadRequestHttpException $exception) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            $response = [
                'ok' => false,
                'error_message' => 'Bad request'
            ];
        } catch(\Throwable $throwable) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = [
                'ok' => false,
                'error_message' => 'Internal server error'
            ];
        }

        return new JsonResponse($response, $statusCode);
    }
}