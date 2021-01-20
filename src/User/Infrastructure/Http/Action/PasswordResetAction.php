<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Command\RequestPasswordResetCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PasswordResetAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);
        $command = new RequestPasswordResetCommand(
            $paramFetcher->getDataParameter('login')
        );

        $this->dispatch($command);
        return $this->respondWithAccepted();
    }
}