<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Command\RegisterUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegisterUserAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);
        $command = new RegisterUserCommand(
            Uuid::random()->value(),
            $paramFetcher->getDataParameter('username', ''),
            $paramFetcher->getDataParameter('email', ''),
            $paramFetcher->getDataParameter('password', '')
        );

        $this->dispatch($command);

        return $this->respondWithCreated([
            'user' => [
                'id' => $command->getUserId(),
                'username' => $command->getUsername(),
                'email' => $command->getEmail(),
            ]
        ]);
    }
}