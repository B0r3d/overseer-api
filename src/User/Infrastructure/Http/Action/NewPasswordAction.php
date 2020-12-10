<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Command\NewPasswordCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class NewPasswordAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);
        $this->dispatch(new NewPasswordCommand(
            $paramFetcher->getDataParameter('password_reset_token'),
            $paramFetcher->getDataParameter('new_password')
        ));

        return $this->respondWithOk();
    }
}