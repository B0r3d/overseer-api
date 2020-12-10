<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Command\ChangeUserPasswordCommand;
use Overseer\User\Domain\Service\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeUserPasswordAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $userId = $request->get('_user_id');
        $user = $this->getUser();

        if ($user->getId() !== $userId) {
            throw new UnauthorizedException();
        }

        $paramFetcher = $this->getParamFetcher($request);
        $command = new ChangeUserPasswordCommand(
            $user->getId(),
            $paramFetcher->getDataParameter('current_password', ''),
            $paramFetcher->getDataParameter('new_password', ''),
            $request->cookies->get(JWT::REFRESH_TOKEN_COOKIE)
        );

        $this->dispatch($command);

        return $this->respondWithAccepted();
    }
}