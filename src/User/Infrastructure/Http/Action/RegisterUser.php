<?php


namespace Overseer\User\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Overseer\User\Domain\Dto\RegisterUserRequest;
use Overseer\User\Domain\Exception\UserAlreadyExistsException;
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
            $statusCode = 200;
            $content = $this->serializer->serialize([
               'ok' => true,
               'data' => [
                   'username' => $dto->username(),
                   'email' => $dto->email(),
                   'uuid' => $dto->uuid(),
               ],
            ], 'json');
        } catch(UserAlreadyExistsException $exception) {
            $statusCode = 400;
            $content = $this->serializer->serialize([
                'ok' => false,
                'error_message' => 'User already exists'
            ], 'json');
        } catch(BadRequestHttpException $exception) {
            $statusCode = 400;
            $content = $this->serializer->serialize([
                'ok' => false,
                'error_message' => 'Bad request'
            ], 'json');
        } catch(\Throwable $throwable) {
            $statusCode = 500;
            dump($throwable);
            die;
            $content = $this->serializer->serialize([
                'ok' => false,
                'error_message' => 'Internal server error'
            ], 'json');
        }

        return new Response($content, $statusCode);
    }
}