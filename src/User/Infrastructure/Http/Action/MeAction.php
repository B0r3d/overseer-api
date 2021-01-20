<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Dto\UserResource;
use Overseer\User\Domain\Query\GetUserQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MeAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        /** @var SingleObjectResult $result */
        $result = $this->ask(new GetUserQuery($subject->getId()));

        $userResource = new UserResource($result->getData());

        return $this->respondWithOk($userResource);
    }
}