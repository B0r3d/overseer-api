<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Command\DeleteUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteUserAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);

        $this->dispatch(new DeleteUserCommand(
            $request->get('_id'),
            $paramFetcher->getDataParameter('current_password')
        ));

        return $this->respondWithAccepted();
    }
}